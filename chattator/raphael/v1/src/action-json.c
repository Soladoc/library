/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation (JSON-related)
/// @date 23/01/2025

#include "action.h"
#include "util.h"

#define put_error_missing(parent, key) put_error("missing key in " parent ": " key "\n");
#define put_error_missing_or_invalid(parent, key) put_error("missing key or invalid value: " parent " > " key "\n")

static inline serial_t json_object_get_user_id(json_object *user_key, db_t *db);
static inline serial_t get_api_key(uuid4_t *api_key, json_object *with, config_t *cfg, db_t *db);

errstatus_t action_parse(struct action *action, json_object *obj, config_t *cfg, db_t *db) {
    char const *name = json_object_get_string(json_object_object_get(obj, "do"));
    if (!name) {
        put_error_missing_or_invalid("action", "do");
        return errstatus_handled;
    }

    json_object *with = json_object_object_get(obj, "with");
    if (!with) {
        put_error_missing_or_invalid("action", "with");
        return errstatus_handled;
    }

    // todo..

    if (streq(name, "login")) {
        action->type = action_type_login;

        errstatus_t err = get_api_key(&action->with.login.api_key, with, cfg, db);
        switch (err) {
        case errstatus_error: put_error_missing_or_invalid("login", "api_key"); [[fallthrough]];
        case errstatus_handled: return err;
        default:;
        }

        char const *password_hash = json_object_get_string(json_object_object_get(with, "password_hash"));
        if (!password_hash) {
            put_error_missing_or_invalid("login", "password_hash");
            return errstatus_handled;
        }
        strncpy(action->with.login.password_hash, password_hash, sizeof action->with.login.password_hash - 1);
        action->with.login.password_hash[sizeof action->with.login.password_hash - 1] = '\0';
    } else if (streq(name, "logout")) {
        action->type = action_type_logout;

        if (!(action->with.logout.token = json_object_get_uint64(json_object_object_get(with, "token")))) {
            put_error_missing_or_invalid("logout", "token");
        }
    } else if (streq(name, "whois")) {
        action->type = action_type_whois;

        errstatus_t err = get_api_key(&action->with.whois.api_key, with, cfg, db);
        switch (err) {
        case errstatus_error: put_error_missing_or_invalid("whois", "api_key"); [[fallthrough]];
        case errstatus_handled: return err;
        default:;
        }

        serial_t res = json_object_get_user_id(json_object_object_get(with, "user"), db);
        switch (res) {
        case errstatus_error: put_error_missing_or_invalid("whois", "user"); [[fallthrough]];
        case errstatus_handled: return res;
        default: action->with.whois.user_id = res;
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
        put_error("unknown action: %s\n", name);
        return errstatus_handled;
    }

    return true;
}

json_object *response_to_json(struct response *response) {
    // todo...

    json_object *obj = json_object_new_object(), *body = json_object_new_object();

#define add_key(o, k, v) json_object_object_add_ex(o, k, v, JSON_C_OBJECT_ADD_KEY_IS_NEW | JSON_C_OBJECT_KEY_IS_CONSTANT)

    add_key(obj, "status", json_object_new_int(response->status));
    add_key(obj, "has_next_page", json_object_new_boolean(response->has_next_page));
    add_key(obj, "body", body);

    switch (response->type) {
    case action_type_login:

        break;
    case action_type_logout:

        break;
    case action_type_whois:
        add_key(body, "user_id", json_object_new_int(response->body.whois.user_id));
        add_key(body, "email", json_object_new_string(response->body.whois.email));
        add_key(body, "last_name", json_object_new_string(response->body.whois.last_name));
        add_key(body, "first_name", json_object_new_string(response->body.whois.first_name));
        add_key(body, "display_name", json_object_new_string(response->body.whois.display_name));
        add_key(body, "kind", json_object_new_int(response->body.whois.kind));
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

#undef add_key

    return obj;
}

serial_t get_api_key(uuid4_t *api_key, json_object *with, config_t *cfg, db_t *db) {
    char const *repr = json_object_get_string(json_object_object_get(with, "api_key"));
    if (!repr) return errstatus_error;

    serial_t res;
    if (errstatus_ok != (res = uuid4_from_repr(api_key, repr))) return res;

    if (errstatus_error == (res = config_verify_api_key(cfg, *api_key, db))) {
        put_error("api key invalid: %s", repr);
    }

    return res;
}

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
    default:;
    }
    return errstatus_error;
}
