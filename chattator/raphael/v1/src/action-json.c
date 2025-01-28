/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation (JSON-related)
/// @date 23/01/2025

#include "action.h"
#include "util.h"

#define put_error_wrong_type(parent, key, value, expectedType) put_error("type error: %s > " key ": expected %s, got %s\n", parent, json_type_to_name(json_object_get_type(value)), json_type_to_name(expectedType));

#define put_error_missing_or_invalid(parent, key) put_error("missing key or invalid value: " parent " > " key "\n")

#define put_error_invalid(parent, key, msg, ...) put_error("invalid value at " parent " > " key ": " msg, __VA_ARGS__)

static inline serial_t json_object_get_user_id(json_object *obj_user_key, db_t *db);

static inline token_t get_token(char const *parent, json_object *obj_with) {
    json_object *obj_token = json_object_object_get(obj_with, "token");
    if (!json_object_is_type(obj_token, json_type_int)) {
        put_error_wrong_type(parent, "with", obj_token, json_type_int);
        return 0;
    }
    return json_object_get_int64(obj_token);
} 

static inline serial_t try_get_api_key(uuid4_t *api_key, json_object *obj_with) {
    char const *repr = json_object_get_string(json_object_object_get(obj_with, "api_key"));
    return repr ? uuid4_from_repr(api_key, repr) : errstatus_error;
}

errstatus_t action_parse(struct action *action, json_object *obj, cfg_t *cfg, db_t *db) {
    json_object *obj_do = json_object_object_get(obj, "do");
    char const *do_ = json_object_get_string(obj_do);
    if (!do_) {
        put_error_wrong_type("action", "do", obj_do, json_type_string);
        return errstatus_handled;
    }

    json_object *obj_with = json_object_object_get(obj, "with");
    if (!obj_with) {
        put_error_wrong_type("action", "with", obj_with, json_type_object);
        return errstatus_handled;
    }

    if (streq(do_, "login")) {
        action->type = action_type_login;

        // api_key
        errstatus_t err = try_get_api_key(&action->with.login.api_key, obj_with);
        switch (err) {
        case errstatus_error: put_error_missing_or_invalid("login", "api_key"); [[fallthrough]];
        case errstatus_handled: return err;
        default:;
        }

        // password_hash
        char const *password_hash = json_object_get_string(json_object_object_get(obj_with, "password_hash"));
        if (!password_hash) {
            put_error_missing_or_invalid("login", "password_hash");
            return errstatus_handled;
        }
        strncpy(action->with.login.password_hash, password_hash, sizeof action->with.login.password_hash - 1);
        action->with.login.password_hash[sizeof action->with.login.password_hash - 1] = '\0';
    } else if (streq(do_, "logout")) {
        action->type = action_type_logout;

        // token
        if (!(action->with.logout.token = get_token("login", obj_with))) {
            return errstatus_handled;
        }
    } else if (streq(do_, "whois")) {
        action->type = action_type_whois;

        // api_key
        errstatus_t err = try_get_api_key(&action->with.whois.api_key, obj_with);
        switch (err) {
        case errstatus_error: put_error_missing_or_invalid("whois", "api_key"); [[fallthrough]];
        case errstatus_handled: return err;
        default:;
        }

        // user
        serial_t res = json_object_get_user_id(json_object_object_get(obj_with, "user"), db);
        switch (res) {
        case errstatus_error: put_error_missing_or_invalid("whois", "user"); [[fallthrough]];
        case errstatus_handled: return res;
        default: action->with.whois.user_id = res;
        }
    } else if (streq(do_, "send")) {
        action->type = action_type_send;

        // token
        if (!(action->with.logout.token = get_token("send", obj_with))) {
            return errstatus_handled;
        }

        // content
        json_object *obj_content = json_object_object_get(obj, "content");
        char const *content = json_object_get_string(obj_content);
        int const content_len = json_object_get_string_len(obj_content);
        if (!json_object_is_type(obj, json_type_string) || !content) {
            put_error_wrong_type("send", "content", obj_content, json_type_string);
        }
        if (content_len > config_max_msg_length(cfg)) {
            put_error_invalid("send", "content", "length (%d) is longer than maximum (%d)",
                content_len,
                config_max_msg_length(cfg));
        }
        
        strncpy(action->with.send.content, content, content_len);

        // dest
        serial_t res = json_object_get_user_id(json_object_object_get(obj_with, "dest"), db);
        switch (res) {
        case errstatus_error: put_error_missing_or_invalid("send", "dest"); [[fallthrough]];
        case errstatus_handled: return res;
        default: action->with.send.dest_user_id = res;
        }
    } else if (streq(do_, "motd")) {
        action->type = action_type_motd;

    } else if (streq(do_, "inbox")) {
        action->type = action_type_inbox;

    } else if (streq(do_, "outbox")) {
        action->type = action_type_outbox;

    } else if (streq(do_, "edit")) {
        action->type = action_type_edit;

    } else if (streq(do_, "rm")) {
        action->type = action_type_rm;

    } else if (streq(do_, "block")) {
        action->type = action_type_block;

    } else if (streq(do_, "unblock")) {
        action->type = action_type_unblock;

    } else if (streq(do_, "ban")) {
        action->type = action_type_ban;
    } else if (streq(do_, "unban")) {
        action->type = action_type_unban;
    } else {
        put_error("unknown action: %s\n", do_);
        return errstatus_handled;
    }

    return true;
}

json_object *response_to_json(struct response *response) {
    // todo...

    json_object *obj = json_object_new_object(), *obj_body = json_object_new_object();

#define add_key(o, k, v) json_object_object_add_ex(o, k, v, JSON_C_OBJECT_ADD_KEY_IS_NEW | JSON_C_OBJECT_KEY_IS_CONSTANT)

    add_key(obj, "status", json_object_new_int(response->status));
    add_key(obj, "has_next_page", json_object_new_boolean(response->has_next_page));
    add_key(obj, "body", obj_body);

    switch (response->type) {
    case action_type_login:

        break;
    case action_type_logout:

        break;
    case action_type_whois:
        add_key(obj_body, "user_id", json_object_new_int(response->body.whois.user_id));
        add_key(obj_body, "email", json_object_new_string(response->body.whois.email));
        add_key(obj_body, "last_name", json_object_new_string(response->body.whois.last_name));
        add_key(obj_body, "first_name", json_object_new_string(response->body.whois.first_name));
        add_key(obj_body, "display_name", json_object_new_string(response->body.whois.display_name));
        add_key(obj_body, "kind", json_object_new_int(response->body.whois.kind));
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

serial_t json_object_get_user_id(json_object *obj_user_key, db_t *db) {
    switch (json_object_get_type(obj_user_key)) {
    case json_type_int: return json_object_get_int(obj_user_key);
    case json_type_string: {
        if (json_object_get_string_len(obj_user_key) > max(EMAIL_LENGTH, PSEUDO_LENGTH)) break;
        const char *email_or_pseudo = json_object_get_string(obj_user_key);
        return strchr(email_or_pseudo, '@')
                 ? db_get_user_id_by_email(db, email_or_pseudo)
                 : db_get_user_id_by_pseudo(db, email_or_pseudo);
    }
    default:;
    }
    return errstatus_error;
}
