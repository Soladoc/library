/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation
/// @date 23/01/2025

#include "action.h"
#include "db.h"

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
