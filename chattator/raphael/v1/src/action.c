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

static inline serial_t json_object_get_account_id(json_object *account_key);

bool action_parse(struct action *action, json_object *obj) {
    char const *name = json_object_get_string(json_object_object_get(obj, "do"));
    if (!name) fail_missing_or_invalid("action", "do");

    json_object *with = json_object_object_get(obj, "with");

    // todo..

    if (streq(name, "login")) {
        action->type = action_type_login;

        char const *api_key = json_object_get_string(json_object_object_get(with, "api_key"));
        if (!api_key) fail_missing_or_invalid("login", "api_key");
        uuid4_from_repr(&action->login.api_key, api_key);

        char const *password_hash = json_object_get_string(json_object_object_get(with, "password_hash"));
        if (!password_hash) fail_missing_or_invalid("login", "password_hash");
        strncpy(action->login.password_hash, api_key, sizeof action->login.password_hash - 1);
        action->login.password_hash[sizeof action->login.password_hash - 1] = '\0';
    } else if (streq(name, "logout")) {
        action->type = action_type_logout;

        if (!(action->logout.token = json_object_get_uint64(json_object_object_get(with, "token")))) {
            fail_missing_or_invalid("logout", "token");
        }
    } else if (streq(name, "whois")) {
        action->type = action_type_whois;

        char const *api_key = json_object_get_string(json_object_object_get(with, "api_key"));
        if (!api_key) fail_missing_or_invalid("whois", "api_key");
        uuid4_from_repr(&action->whois.api_key, api_key);

        if (!(action->whois.user_id = json_object_get_account_id(json_object_object_get(with, "user")))) {
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
}
#endif // NDEBUG

serial_t json_object_get_account_id(json_object *account_key) {
    switch (json_object_get_type(account_key)) {

    case json_type_int: return json_object_get_int(account_key);
    case json_type_string: {
        if (json_object_get_string_len(account_key) > max(EMAIL_LENGTH, PSEUDO_LENGTH)) break;
        const char *email_or_pseudo = json_object_get_string(account_key);
        return strchr(email_or_pseudo, '@')
                 ? db_get_account_id_by_email(email_or_pseudo)
                 : db_get_account_id_by_pseudo(email_or_pseudo);
    }
    default: break;
    }
    return 0;
}
