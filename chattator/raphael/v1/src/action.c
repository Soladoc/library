/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation
/// @date 23/01/2025

#include "action.h"
#include "db.h"
#include "util.h"

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
    config_verify_api_key_t result;
    switch (config_verify_api_key(&result, cfg, api_key, db)) {
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

void action_destroy(action_t const *action) {
    switch (action->type) {
    case action_type_login: free(action->with.login.password); break;
    case action_type_send: free(action->with.send.content); break;
    case action_type_edit: free(action->with.edit.new_content); break;
    default: break;
    }
}

bool action_evaluate(action_t const *action, response_t *rep, cfg_t *cfg, db_t *db, server_t *server) {
    // Identify user
    user_identity_t user;

#define fail(return_status)          \
    do {                             \
        rep->status = return_status; \
        return true;                 \
    } while (0)

#define check_role(allowed_roles) \
    if (!(user.role & (allowed_roles))) fail(status_forbidden)

#define auth_api_key(action_name)                                                      \
    do {                                                                               \
        if (uuid4_eq(action->with.action_name.api_key, *config_admin_api_key(cfg))) {  \
            user.role = role_admin;                                                    \
            user.id = 0;                                                               \
            return errstatus_ok;                                                       \
        }                                                                              \
        switch (db_verify_user_api_key(db, &user, action->with.action_name.api_key)) { \
        case errstatus_handled: fail(status_unauthorized);                             \
        case errstatus_error: fail(status_forbidden);                                  \
        default:;                                                                      \
        }                                                                              \
    } while (0)

#define auth_token(action_name)                                                                                  \
    do {                                                                                                         \
        if (!(user.id = server_verify_token(server, action->with.action_name.token))) fail(status_unauthorized); \
        int user_role = db_get_user_role(db, user.id);                                                           \
        switch (user_role) {                                                                                     \
        case errstatus_handled: return false;                                                                    \
        case errstatus_error: fail(status_forbidden);                                                            \
        }                                                                                                        \
        user.role = user_role;                                                                                   \
    } while (0)
    switch (rep->type = action->type) {
        // clang-format off
    case action_type_login:   auth_api_key(login); check_role(role_all); break;
    case action_type_logout:  auth_token(logout);  check_role(role_all); break;
    case action_type_whois:   auth_api_key(whois); check_role(role_all); break;
    case action_type_send:    auth_token(send);    check_role(role_all); break;
    case action_type_motd:    auth_token(motd);    check_role(role_all); break;
    case action_type_inbox:   auth_token(inbox);   check_role(role_all); break;
    case action_type_outbox:  auth_token(outbox);  check_role(role_all); break;
    case action_type_edit:    auth_token(edit);    check_role(role_all); break;
    case action_type_rm:      auth_token(rm);      check_role(role_admin | role_pro); break;
    case action_type_block:   auth_token(block);   check_role(role_admin | role_pro); break;
    case action_type_unblock: auth_token(unblock); check_role(role_admin | role_pro); break;
    case action_type_ban:     auth_token(ban);     check_role(role_admin | role_pro); break;
    case action_type_unban:   auth_token(unban);   check_role(role_admin | role_pro); break;
        // clang-format on
    }

#undef auth_api_key
#undef auth_token

    // "Turnstile" rate limit

    if (!server_turnstile_rate_limit(server, user.id, cfg)) fail(status_too_many_requests);

    // Build answer

    rep->has_next_page = false; // default most of the time - who cares if we set it twice

    switch (action->type) {
#define DO login
    case action_type(DO):
        switch (db_check_password(db, user.id, action->with.DO.password)) {
        case errstatus_error: fail(status_unauthorized);
        case errstatus_handled: return false;
        default:;
        }
        if (!(rep->body.DO.token = server_login(server, user.id))) fail(status_internal_server_error);
        break;
#undef DO
#define DO logout
    case action_type(DO):
        if (!server_logout(server, action->with.DO.token)) fail(status_unauthorized);
        break;
#undef DO
#define DO whois
    case action_type(DO):
        rep->body.DO.user_id = action->with.DO.user_id;
        switch (db_get_user(db, &rep->body.DO)) {
        case errstatus_error: fail(status_not_found);
        case errstatus_handled: return false;
        default:;
        }
        break;
#undef DO
#define DO send
    case action_type(DO):

        break;
#undef DO
#define DO motd
    case action_type(DO):

        break;
#undef DO
#define DO inbox
    case action_type(DO):

        break;
#undef DO
#define DO outbox
    case action_type(DO):

        break;
#undef DO
#define DO edit
    case action_type(DO):

        break;
#undef DO
#define DO rm
    case action_type(DO):

        break;
#undef DO
#define DO block
    case action_type(DO):

        break;
#undef DO
#define DO unblock
    case action_type(DO):

        break;
#undef DO
#define DO ban
    case action_type(DO):

        break;
#undef DO
#define DO unban
    case action_type(DO):

        break;
    }

    rep->status = status_ok;
    return true;
}

#ifndef NDEBUG
void action_explain(action_t const *action, FILE *output) {
    // todo...

    switch (action->type) {
    case action_type_login:
        fprintf(output, "login api_key=");
        uuid4_put(action->with.login.api_key, output);
        fprintf(output, " password=%s\n", action->with.login.password);
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
