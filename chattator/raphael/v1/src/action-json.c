/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation (JSON-related)
/// @date 23/01/2025

#include "action.h"
#include "json-helpers.h"
#include "util.h"

// error: DO.with: missing key: KEY
#define put_error_arg_missing(do_, key, ...) put_error_json_missing_key(key, do_ ".with" __VA_OPT__(, ) __VA_ARGS__)
// error: DO.with.key: type: expected TYPE, got ACTUAL
#define put_error_arg_type(type, actual, do_, key, ...) put_error_json_type(type, actual, do_ ".with." key __VA_OPT__(, ) __VA_ARGS__)
// error: DO.with.key: invalid value: MSG
#define put_error_arg_invalid(do_, key, ...) put_error(do_ ".with." key ": invalid value" __VA_OPT__(, ) __VA_ARGS__)
#define put_error_arg_invalid_because(do_, key, reason, ...) put_error(do_ ".with." key ": invalid value: " reason __VA_OPT__(, ) __VA_ARGS__)

/// @return @ref serial_t the user ID.
/// @return @ref errstatus_handled On error
/// @return @ref errstatus_error On error
static inline serial_t get_user_id(char const *do_, json_object *obj_with, char const *key, db_t *db) {
    json_object *obj_user;
    if (!json_object_object_get_ex(obj_with, key, &obj_user)) {
        put_error_arg_missing("%s", "%s", do_, key);
        return 0;
    }
    switch (json_object_get_type(obj_user)) {
    case json_type_int: return json_object_get_int(obj_user);
    case json_type_string: {
        if (json_object_get_string_len(obj_user) > max(EMAIL_LENGTH, PSEUDO_LENGTH)) break;
        const char *email_or_pseudo = json_object_get_string(obj_user);
        return strchr(email_or_pseudo, '@')
                 ? db_get_user_id_by_email(db, email_or_pseudo)
                 : db_get_user_id_by_pseudo(db, email_or_pseudo);
    }
    default:;
    }
    return errstatus_error;
}

/// @return a string 
/// @return @c NULL on error
static inline char *get_content(char const *do_, json_object *obj_with, char const *key, cfg_t const *cfg) {
    json_object *obj_content;
    if (!json_object_object_get_ex(obj_with, key, &obj_content)) {
        put_error_arg_missing("%s", "%s", do_, key);
        return NULL;
    }
    char const *content;
    int content_len;
    if (!json_object_get_string_strict(obj_content, &content, &content_len)) {
        put_error_arg_type(json_type_string, json_object_get_type(obj_content), "%s", "%s", do_, key);
        return NULL;
    }
    if (content_len > config_max_msg_length(cfg)) {
        put_error_arg_invalid_because("%s", "%s", "length (%d) is longer than maximum (%d)",
            do_, content, content_len, config_max_msg_length(cfg));
        return NULL;
    }
    char *content_copy = strndup(content, content_len);
    if (!content_copy) errno_exit("strndup");
    return content_copy;
}

/// @return a page number
/// @return @c 0 on error
static inline page_number_t get_page(char const *do_, json_object *obj_with) {
    json_object *obj_page;
    if (!json_object_object_get_ex(obj_with, "page", &obj_page)) {
        put_error_arg_missing("%s", "page", do_);
        return 0;
    }
    page_number_t page;
    if (!json_object_get_int_strict(obj_page, &page)) {
        put_error_arg_type(json_type_int, json_object_get_type(obj_page), "%s", "page", do_);
        return 0;
    }
    return page;
}

/// @return a message ID
/// @return @c 0 on error
static inline serial_t get_msg_id(char const *do_, json_object *obj_with) {
    json_object *obj_msg_id;
    if (!json_object_object_get_ex(obj_with, "msg_id", &obj_msg_id)) {
        put_error_arg_missing("%s", "msg_id", do_);
        return 0;
    }
    serial_t msg_id;
    if (!json_object_get_int_strict(obj_msg_id, &msg_id)) {
        put_error_arg_type(json_type_int, json_object_get_type(obj_msg_id), "%s", "msg_id", do_);
        return 0;
    }
    return msg_id;
}

/// @return a token
/// @return @c 0 on error
static inline token_t get_token(char const *do_, json_object *obj_with) {
    json_object *obj_token;
    if (!json_object_object_get_ex(obj_with, "token", &obj_token)) {
        put_error_arg_missing("%s", "token", do_);
        return 0;
    }
    token_t token;
    if (!json_object_get_int64_strict(obj_token, &token)) {
        put_error_arg_type(json_type_int, json_object_get_type(obj_token), "%s", "token", do_);
        return 0;
    }
    return token;
}

static inline bool get_api_key(uuid4_t *out_api_key, char const *do_, json_object *obj_with) {
    json_object *obj_api_key;
    if (!json_object_object_get_ex(obj_with, "api_key", &obj_api_key)) {
        put_error_arg_missing("%s", "api_key", do_);
        return false;
    }
    char const *repr;
    if (!json_object_get_string_strict(obj_api_key, &repr, NULL)) {
        put_error_arg_type(json_type_string, json_object_get_type(obj_api_key), "%s", "api_key", do_);
        return false;
    }
    if (!uuid4_from_repr(out_api_key, repr)) {
        put_error_arg_invalid_because("%s", "api_key", "invalid API key", do_);
        return false;
    }
    return true;
}

bool action_parse(action_t *out_action, json_object *obj, cfg_t *cfg, db_t *db) {
    json_object *obj_do;
    if (!json_object_object_get_ex(obj, "do", &obj_do)) {
        put_error_json_missing_key("do", "action");
        return false;
    }

    char const *do_;
    if (!json_object_get_string_strict(obj_do, &do_, NULL)) {
        put_error_json_type(json_type_string, json_object_get_type(obj_do), "do");
        return false;
    }

    json_object *obj_with;
    if (!json_object_object_get_ex(obj, "with", &obj_with)) {
        put_error_json_missing_key("with", "action");
        return false;
    }

#define ACTION_TYPE CAT(action_type_, DO)
#define DO_IS_ACTION streq(STR(DO), do_)

#define DO login
    if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // api_key
        if (!get_api_key(&out_action->with.DO.api_key, STR(DO), obj_with)) return false;

        // password_hash
        // Check if key exists
        json_object *obj_password_hash;
        if (!json_object_object_get_ex(obj_with, "password_hash", &obj_password_hash)) {
            put_error_arg_missing("password_hash", STR(DO));
            return false;
        }
        // Ensure value has correct type
        char const *password_hash;
        if (!json_object_get_string_strict(obj_password_hash, &password_hash, NULL)) {
            put_error_arg_type(json_type_string, json_object_get_type(obj_password_hash), STR(DO), "password_hash");
            return false;
        }
        // Copy (because json_object value will be destroyed)
        strncpy(out_action->with.DO.password_hash, password_hash, PASSWORD_HASH_LENGTH);
        out_action->with.DO.password_hash[PASSWORD_HASH_LENGTH] = '\0';
#undef DO
#define DO logout
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;
#undef DO
#define DO whois
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // api_key
        if (!get_api_key(&out_action->with.DO.api_key, STR(DO), obj_with)) return false;

        // user
        switch (out_action->with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: put_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: return false;
        default:;
        }
#undef DO
#define DO send
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;

        // content
        if (!(out_action->with.DO.content = get_content(STR(DO), obj_with, "content", cfg))) return false;

        // dest
        switch (out_action->with.DO.dest_user_id = get_user_id(STR(DO), obj_with, "dest", db)) {
        case errstatus_error: put_error_arg_invalid(STR(DO), "dest"); [[fallthrough]];
        case errstatus_handled: return false;
        }
#undef DO
#define DO motd
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;
#undef DO
#define DO inbox
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;

        // page
        if (!(out_action->with.DO.page = get_page(STR(DO), obj_with))) return false;
#undef DO
#define DO outbox
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;

        // page
        if (!(out_action->with.DO.page = get_page(STR(DO), obj_with))) return false;
#undef DO
#define DO edit
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;

        // msg_id
        if (!(out_action->with.DO.msg_id = get_msg_id(STR(DO), obj_with))) return false;

        // new_content
        if (!(out_action->with.DO.new_content = get_content(STR(DO), obj_with, "new_content", cfg))) return false;
#undef DO
#define DO rm
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;

        // msg_id
        if (!(out_action->with.DO.msg_id = get_msg_id(STR(DO), obj_with))) return false;
#undef DO
#define DO block
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;

        // user
        switch (out_action->with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: put_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: return false;
        }
#undef DO
#define DO unblock
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;

        // user
        switch (out_action->with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: put_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: return false;
        }
#undef DO
#define DO ban
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;

        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;

        // user
        switch (out_action->with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: put_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: return false;
        }
#undef DO
#define DO unban
    } else if (DO_IS_ACTION) {
        out_action->type = ACTION_TYPE;
        // token
        if (!(out_action->with.DO.token = get_token(STR(DO), obj_with))) return false;

        // user
        switch (out_action->with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: put_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: return false;
        }
    } else {
        put_error("unknown action: %s\n", do_);
        return false;
    }

    return true;
}

json_object *response_to_json(response_t *response) {
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
