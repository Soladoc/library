/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation
/// @date 23/01/2025

#include "action.h"
#include "db.h"
#include "util.h"

void put_role(role_flags_t role, FILE *stream) {
    if (role & role_admin) fputs("admin", stream);
    if (role & role_membre) fputs(role & role_admin ? " or membre" : "membre", stream);
    if (role & role_pro) fputs(role & (role_admin | role_membre) ? " or professionnel" : "professionnel", stream);
}

/// @brief Check the API key and put an erorr if it is invalid or the user doesn't have access.
///
/// @param cfg Configuration.
/// @param db Database.
/// @param api_key API key.
/// @param allowed_roles Bit-wise flags of the roles the user is allowed to have.
/// @return @ref 0 The user's role doesn't correspond to @p allowed_roles, The API key is invalid, or another error occured and was handled.
/// @return The ID of the user who owns this API key.
static inline serial_t check_api_key(cfg_t *cfg, db_t *db, api_key_t api_key, role_flags_t allowed_roles) {
    errstatus_t err;
    config_verify_api_key_t result;
    switch (err = config_verify_api_key(&result, cfg, api_key, db)) {
    case errstatus_error: {
        char repr[UUID4_REPR_LENGTH];
        put_error("api key invalid: %" PRIuuid4_repr, uuid4_repr(api_key, repr));
        [[fallthrough]];
    }
    case errstatus_handled: return 0;
    default:;
    }
    if (!(result.user_role & allowed_roles)) {
        put_error("unauthorized: user is ");
        put_role(result.user_role, stderr);
        fputs(", must be ", stderr);
        put_role(allowed_roles, stderr);
        putc('\n', stderr);
        return 0;
    }
    return result.user_id;
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

bool action_evaluate(action_t const *action, response_t *response, cfg_t *cfg, db_t *db) {
    // todo...

    switch (response->type = action->type) {
    case action_type_login:
        break;
    case action_type_logout:
        break;
    case action_type_whois:
        response->has_next_page = false;
        response->status = status_ok;
        response->body.whois.user_id = action->with.whois.user_id;
        if (!db_get_user(db, &response->body.whois)) return false;
        break;
    case action_type_send:
        break;
    case action_type_motd:
        break;
    case action_type_inbox:
        break;
    case action_type_outbox:
        break;
    case action_type_edit:
        break;
    case action_type_rm:
        break;
    case action_type_block:
        break;
    case action_type_unblock:
        break;
    case action_type_ban:
        break;
    case action_type_unban:
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
