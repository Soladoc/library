/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation
/// @date 23/01/2025

#include "action.h"
#include "db.h"
#include "util.h"

#define putln_error_rate_limit_exceeded(action_name, remaining_seconds) \
    put_error(action_name, ": rate limit exceeded. Next request in %d second%s.", remaining_seconds, remaining_seconds == 1 ? "s" : "");

/// @brief Check the API key and put an erorr if it is invalid or the user doesn't have access.
///
/// @param action_name Action name.
/// @param cfg Configuration.
/// @param db Database.
/// @param api_key API key.
/// @param allowed_roles Bit-wise flags of the roles the user is allowed to have.
/// @return @ref 0 The user's role doesn't correspond to @p allowed_roles, The API key is invalid, or another error occured and was handled.
/// @return The ID of the user who owns this API key.
static inline serial_t check_api_key(const char* action_name, cfg_t *cfg, db_t *db, api_key_t api_key, role_flags_t allowed_roles) {
    errstatus_t err;
    config_verify_api_key_t result;
    switch (err = config_verify_api_key(&result, cfg, api_key, db)) {
    case errstatus_error: {
        char repr[UUID4_REPR_LENGTH];
        put_error("%s: api key invalid: %" PRIuuid4_repr, action_name, uuid4_repr(api_key, repr));
        [[fallthrough]];
    }
    case errstatus_handled: return 0;
    default:;
    }
    if (!(result.user_role & allowed_roles)) {
        put_error("%s: unauthorized: user is ", action_name);
        put_role(result.user_role, stderr);
        fputs(", must be ", stderr);
        put_role(allowed_roles, stderr);
        putc('\n', stderr);
        return 0;
    }
    return result.user_id;
}

/// @brief Checks and increments the rate limit for the specified user.
///
/// @param user_id The ID of the user performing the request.
/// @return @c true The turnstile passes (the rate limit hasn't been reached)
/// @return @c false The turnstile blocks (the rate limit has been reached). An error has been put.
static inline bool turnstile_rate_limit(serial_t user_id)
{
    // todo
    return true;
}

static inline serial_t check_token(const char *action_name, cfg_t *cfg, db_t *db, token_t token, role_flags_t allowed_roles)
{
    // todo
    put_error("%s: check_token not implemented", action_name);
    return 0;
}

void action_destroy(action_t const *action) {
    switch (action->type) {
    case action_type_send:
        free(action->with.send.content);
        break;
    case action_type_edit:
        free(action->with.edit.new_content);
        break;
    default:
        break;
    }
}

bool action_evaluate(action_t const *action, response_t *rep, cfg_t *cfg, db_t *db) {
    // todo...
    serial_t user_id;

    switch (rep->type = action->type) {
#define DO login
    case action_type(DO):
        if (!(user_id = check_api_key(STR(DO), cfg, db, action->with.DO.api_key, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO logout
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO whois
    case action_type(DO):
        if (!(user_id = check_api_key(STR(DO), cfg, db, action->with.DO.api_key, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        rep->has_next_page = false;
        rep->status = status_ok;
        rep->body.DO.user_id = action->with.DO.user_id;
        if (!db_get_user(db, &rep->body.DO)) return false;
        break;
#undef DO
#define DO send
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO motd
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO inbox
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO outbox
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO edit
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO rm
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO block
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO unblock
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO ban
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
#undef DO
#define DO unban
    case action_type(DO):
        if (!(user_id = check_token(STR(DO), cfg, db, action->with.DO.token, role_all))) return false;
        if (!turnstile_rate_limit(user_id)) return false;

        break;
    }

    return true;
}

#ifndef NDEBUG
void action_explain(action_t const *action, FILE *output) {
    // todo...

    switch (action->type) {
    case action_type_login:
        fprintf(output, "login api_key=");
        uuid4_put(action->with.login.api_key, output);
        fprintf(output, " password_hash=%s\n", action->with.login.password_hash);
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