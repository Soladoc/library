/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation
/// @date 23/01/2025

#include "action.h"
#include "db.h"

/*
enum { errstatus_unauthorized = min_errstatus - 1 };
/// @brief Check the API key and put an erorr if it is invalid or the user doesn't have access.
/// 
/// @param cfg Configuration.
/// @param db Database.
/// @param with Args object.
/// @param allowed_roles Bit-wise flags of the roles the user is allowed to have.
/// @return @ref errstatus_unauthorized The user's role doesn't correspond to @p allowed_roles.
/// @return @ref errstatus_handled An error has occured; a message has been shown. Propagate the error.
/// @return @ref errstatus_error The API key is invalid.
/// @return The ID of the user who owns this API key.
static inline serial_t check_api_key(config_t *cfg, db_t *db, json_object *with, role_flags_t allowed_roles);

serial_t check_api_key(config_t *cfg, db_t *db, json_object *with, role_flags_t allowed_roles) {
    char const *repr = json_object_get_string(json_object_object_get(with, "api_key"));
    if (!repr) return errstatus_error;
    api_key_t api_key;
    serial_t err = uuid4_from_repr(&api_key, repr);
    if (errstatus_ok != err) return err;
    config_verify_api_key_t result;
    switch (err = config_verify_api_key(&result, cfg, api_key, db)) {
    case errstatus_error: put_error("api key invalid: %s", repr); [[fallthrough]];
    case errstatus_handled: return err;
    default:;
    }
    return result.user_role & allowed_roles ? result.user_id : errstatus_unauthorized;
}*/

void action_destroy(struct action const *action) {
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

errstatus_t action_evaluate(struct action const *action, struct response *response, db_t *db) {
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

    return errstatus_ok;
}

#ifndef NDEBUG
void action_explain(struct action const *action, FILE *output) {
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
