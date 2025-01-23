/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation
/// @date 23/01/2025

#include "action.h"
#include "db.h"
#include "util.h"

#define fail(...)                     \
    do {                              \
        fprintf(stderr, __VA_ARGS__); \
        return false;                 \
    } while (0)

#define fail_missing(parent, key) fail("error: missing key in " parent ": " key "\n")

#define fail_missing_or_invalid(parent, key) fail("error: missing key or invalid value in " parent ": " key "\n")

static inline serial_t json_object_get_user_id(json_object *user_key, db_t *db);

static inline bool get_api_key(uuid4_t *api_key, json_object *with, db_t *db) {
    char const *repr = json_object_get_string(json_object_object_get(with, "api_key"));
    return repr && uuid4_from_repr(api_key, repr) && db_verify_api_key(db, *api_key);
}

bool action_parse(struct action *action, json_object *obj, db_t *db) {
    char const *name = json_object_get_string(json_object_object_get(obj, "do"));
    if (!name) fail_missing_or_invalid("action", "do");

    json_object *with = json_object_object_get(obj, "with");

    // todo..

    if (streq(name, "login")) {
        action->type = action_type_login;

        if (!get_api_key(&action->login.api_key, with, db)) {
            fail_missing_or_invalid("login", "api_key");
        }

        char const *password_hash = json_object_get_string(json_object_object_get(with, "password_hash"));
        if (!password_hash) fail_missing_or_invalid("login", "password_hash");
        strncpy(action->login.password_hash, password_hash, sizeof action->login.password_hash - 1);
        action->login.password_hash[sizeof action->login.password_hash - 1] = '\0';
    } else if (streq(name, "logout")) {
        action->type = action_type_logout;

        if (!(action->logout.token = json_object_get_uint64(json_object_object_get(with, "token")))) {
            fail_missing_or_invalid("logout", "token");
        }
    } else if (streq(name, "whois")) {
        action->type = action_type_whois;

        if (!get_api_key(&action->whois.api_key, with, db)) {
            fail_missing_or_invalid("whois", "api_key");
        }

        if (!(action->whois.user_id = json_object_get_user_id(json_object_object_get(with, "user"), db))) {
            fail_missing_or_invalid("whois", "user");
        }
    } else if (streq(name, "send")) {
        action->type = action_type_send;

    } else if (streq(name, "motd")) {
        action->type = action_type_motd;

    } else if (streq(name, "inbox")) {
        action->type = action_type_inbox;

    } else if (streq(name, "outbox")) {
        action->type = action_type_outbox;

    } else if (streq(name, "edit")) {
        action->type = action_type_edit;

    } else if (streq(name, "rm")) {
        action->type = action_type_rm;

    } else if (streq(name, "block")) {
        action->type = action_type_block;

    } else if (streq(name, "unblock")) {
        action->type = action_type_unblock;

    } else if (streq(name, "ban")) {
        action->type = action_type_ban;
    } else if (streq(name, "unban")) {
        action->type = action_type_unban;
    } else {
        fail("unknown action: '%s'", name);
    }

    return true;
}

void action_destroy(struct action const *action) {
    switch (action->type) {
    case action_type_send:
        free(action->send.content);
        break;
    case action_type_edit:
        free(action->edit.new_content);
        break;
    default:
        break;
    }
}

bool action_run(struct action const *action) {
    switch (action->type) {
    case action_type_login:
        return true;
    case action_type_logout:
        return true;
    case action_type_whois:
        return true;
    case action_type_send:
        return true;
    case action_type_motd:
        return true;
    case action_type_inbox:
        return true;
    case action_type_outbox:
        return true;
    case action_type_edit:
        return true;
    case action_type_rm:
        return true;
    case action_type_block:
        return true;
    case action_type_unblock:
        return true;
    case action_type_ban:
        return true;
    case action_type_unban:
        return true;
    }
    unreachable();
}

#ifndef NDEBUG
void action_explain(const struct action *action, FILE *output) {
    switch (action->type) {
    case action_type_login:
        fprintf(output, "login api_key=");
        uuid4_put(action->login.api_key, output);
        fprintf(output, " password_hash=%s\n", action->login.password_hash);
        break;
    case action_type_logout:
        fprintf(output, "logout token=%lu\n", action->logout.token);
        break;
    case action_type_whois:
        fprintf(output, "whois api_key=");
        uuid4_put(action->whois.api_key, output);
        fprintf(output, " user_id=%d\n", action->whois.user_id);
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
    unreachable();
}
#endif // NDEBUG

serial_t json_object_get_user_id(json_object *user_key, db_t *db) {
    switch (json_object_get_type(user_key)) {

    case json_type_int: return json_object_get_int(user_key);
    case json_type_string: {
        if (json_object_get_string_len(user_key) > max(EMAIL_LENGTH, PSEUDO_LENGTH)) break;
        const char *email_or_pseudo = json_object_get_string(user_key);
        return strchr(email_or_pseudo, '@')
                 ? db_get_user_id_by_email(db, email_or_pseudo)
                 : db_get_user_id_by_pseudo(db, email_or_pseudo);
    }
    default: break;
    }
    return 0;
}
