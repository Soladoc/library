/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation
/// @date 23/01/2025

#include <assert.h>
#include <limits.h>
#include <tchattator413/action.h>
#include <tchattator413/db.h>
#include <tchattator413/util.h>

#define putln_error_rate_limit_exceeded(action_name, remaining_seconds) \
    put_error(action_name, ": rate limit exceeded. Next request in %d second%s.", remaining_seconds, remaining_seconds == 1 ? "s" : "");

/*
/// @brief Check an API key and return an error if it is invalid or the user doesn't have access.
/// @param cfg Configuration.
/// @param db Database.
/// @param api_key API key.
/// @param allowed_roles Bit-wise flags of the roles the user is allowed to have.
/// @return @c -2 The API key is invalid.
/// @return @c -1 The user's role doesn't correspond to @p allowed_roles.
/// @return @c 0 another error occured and was handled.
/// @return The ID of the user who owns this API key.
static inline serial_t check_api_key(api_key_t api_key, role_flags_t allowed_roles, cfg_t *cfg, db_t *db) {
    cfg_verify_api_key_t result;
    switch (cfg_verify_api_key(&result, cfg, api_key, db)) {
    case errstatus_handled: return 0;
    case errstatus_error: {
        // char repr[UUID4_REPR_LENGTH];
        // put_error("%s: api key invalid: %" PRIuuid4_repr, action_name, uuid4_repr(api_key, repr));
        return -2;
    }
    default:;
    }
    if (!(result.user_role & allowed_roles)) {
        // this is an error on the client's end - keeping this code to for logging later
        // put_error("%s: unauthorized: user is ", action_name);
        // put_role(result.user_role, stderr);
        // fputs(", must be ", stderr);
        // put_role(allowed_roles, stderr);
        // putc('\n', stderr);
        return -1;
    }
    return result.user_id;
}*/

/// @return @ref errstatus_ok The API key is valid.
/// @return @ref errstatus_error The API key isn't valid.
/// @return @ref errstatus_handled DB error (handled).
static inline errstatus_t auth_api_key(user_identity_t *out_user, uuid4_t api_key, cfg_t *cfg, db_t *db) {
    if (uuid4_eq(api_key, cfg_admin_api_key(cfg))) {
        out_user->role = role_admin;
        out_user->id = 0;
        return errstatus_ok;
    }
    return db_verify_user_api_key(db, out_user, api_key);
}

/// @return @ref errstatus_ok The token is valid.
/// @return @ref errstatus_error The token isn't valid.
/// @return @ref errstatus_handled DB error (handled).
static inline errstatus_t auth_token(user_identity_t *out_user, token_t token, db_t *db, server_t *server) {
    if (!(out_user->id = server_verify_token(server, token))) return errstatus_error;
    int res = db_get_user_role(db, out_user->id);
    // The token exists in server state, so the user ID must exist in the DB.
    // assert(res != errstatus_error); // unless someone messes with the DB in the meantime. We don't have control over that.
    out_user->role = res;
    return MIN(res, errstatus_ok); // reduce ok results to errstatus_ok
}

response_t action_evaluate(action_t const *action, cfg_t *cfg, db_t *db, server_t *server) {
    response_t rep = {};

#define fail(return_status)         \
    do {                            \
        rep.status = return_status; \
        return rep;                 \
    } while (0)

#define check_role(allowed_roles) \
    if (!(user.role & (allowed_roles))) fail(status_forbidden)

#define turnstile_rate_limit() \
    if (!server_turnstile_rate_limit(server, user.id, cfg)) fail(status_too_many_requests)

    // Identify user
    user_identity_t user;

    switch (rep.type = action->type) {
    case action_type_error: {
        rep.status = status_internal_server_error;
        rep.body.error = action->with.error;
        return rep;
    }
#define DO login
    case action_type(DO):
        switch (auth_api_key(&user, action->with.DO.api_key, cfg, db)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_all);
        }

        turnstile_rate_limit();

        switch (db_check_password(db, user.id, action->with.DO.password.val)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default:;
        }
        if (!(rep.body.DO.token = server_login(server, user.id))) fail(status_internal_server_error);
        break;
#undef DO
#define DO logout
    case action_type(DO):
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_all);
        }

        turnstile_rate_limit();

        if (!server_logout(server, action->with.DO.token)) fail(status_unauthorized);
        break;
#undef DO
#define DO whois
    case action_type(DO):
        switch (auth_api_key(&user, action->with.DO.api_key, cfg, db)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_all);
        }

        turnstile_rate_limit();

        rep.body.DO.user_id = action->with.DO.user_id;
        switch (db_get_user(db, &rep.body.DO)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_not_found);
        default:;
        }
        break;
#undef DO
#define DO send
    case action_type(DO): {
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_all);
        }

        turnstile_rate_limit();

        int dest_role;
        switch (dest_role = db_get_user_role(db, action->with.DO.dest_user_id)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_not_found);
        }

        // if message length is greater than maximum
        if (action->with.DO.content.len > cfg_max_msg_length(cfg)) fail(status_payload_too_large);

        if (
            // if sender and dest are the same user
            (user.id == action->with.DO.dest_user_id)
            // if user is client, dest is not pro
            || (user.role & role_membre && !(dest_role & role_pro))
            // if user is pro, dest is not a client having contacted him first
            || (user.role & role_pro && (!(dest_role & role_membre) || !db_count_msg(db, action->with.DO.dest_user_id, user.id)))) {
            fail(status_unprocessable_content);
        }

        switch (rep.body.DO.msg_id = db_send_msg(db, user.id, action->with.DO.dest_user_id, action->with.DO.content.val)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_forbidden);
        }

        break;
    }
#undef DO
#define DO motd
    case action_type(DO):
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_all);
        }

        turnstile_rate_limit();

        break;
#undef DO
#define DO inbox
    case action_type(DO):
        switch (auth_token(&user, action->with.inbox.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_all);
        }

        turnstile_rate_limit();

        break;
#undef DO
#define DO outbox
    case action_type(DO):
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_all);
        }

        turnstile_rate_limit();

        break;
#undef DO
#define DO edit
    case action_type(DO):
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_all);
        }

        turnstile_rate_limit();

        break;
#undef DO
#define DO rm
    case action_type(DO):
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_all);
        }

        turnstile_rate_limit();

        break;
#undef DO
#define DO block
    case action_type(DO):
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_admin | role_pro);
        }

        turnstile_rate_limit();

        break;
#undef DO
#define DO unblock
    case action_type(DO):
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_admin | role_pro);
        }

        turnstile_rate_limit();

        break;
#undef DO
#define DO ban
    case action_type(DO):
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_admin | role_pro);
        }

        turnstile_rate_limit();

        break;
#undef DO
#define DO unban
    case action_type(DO):
        switch (auth_token(&user, action->with.DO.token, db, server)) {
        case errstatus_handled: fail(status_internal_server_error);
        case errstatus_error: fail(status_unauthorized);
        default: check_role(role_admin | role_pro);
        }

        turnstile_rate_limit();

        break;
    }

    rep.status = status_ok;
    return rep;
}

#ifndef NDEBUG
void action_explain(action_t const *action, FILE *output) {
    // todo...

    switch (action->type) {
    case action_type_error:
        fprintf(output, "(none)\n");
        break;
    case action_type_login:
        fprintf(output, "login api_key=");
        uuid4_put(action->with.login.api_key, output);
        fprintf(output, " password=%*s\n", slice_leni(action->with.login.password), action->with.login.password.val);
        break;
    case action_type_logout:
        fprintf(output, "logout token=%lu\n", action->with.logout.token);
        break;
    case action_type_whois:
        fprintf(output, "whois api_key=");
        uuid4_put(action->with.whois.api_key, output);
        fprintf(output, " user_id=%d\n", action->with.whois.user_id);
        break;
    case action_type_send:
        fprintf(output, "send\n");
        break;
    case action_type_motd:
        fprintf(output, "motd\n");
        break;
    case action_type_inbox:
        fprintf(output, "inbox\n");
        break;
    case action_type_outbox:
        fprintf(output, "outbox\n");
        break;
    case action_type_edit:
        fprintf(output, "edit\n");
        break;
    case action_type_rm:
        fprintf(output, "rm\n");
        break;
    case action_type_block:
        fprintf(output, "block\n");
        break;
    case action_type_unblock:
        fprintf(output, "unblock\n");
        break;
    case action_type_ban:
        fprintf(output, "ban\n");
        break;
    case action_type_unban:
        fprintf(output, "unban\n");
        break;
    }
}
#endif // NDEBUG

void put_role(role_flags_t role, FILE *stream) {
    if (role & role_admin) fputs("admin", stream);
    if (role & role_membre) fputs(role & role_admin ? " or membre" : "membre", stream);
    if (role & role_pro) fputs(role & (role_admin | role_membre) ? " or professionnel" : "professionnel", stream);
}
