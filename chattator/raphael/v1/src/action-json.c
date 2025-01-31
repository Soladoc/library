/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation (JSON-related)
/// @date 23/01/2025

#include "action.h"
#include "json-helpers.h"
#include "util.h"
#include <json-c/json.h>

// error: DO.with: missing key: KEY
#define putln_error_arg_missing(action_name, key, ...) putln_error_json_missing_key(key, action_name ".with" __VA_OPT__(, ) __VA_ARGS__)
// error: DO.with.key: type: expected TYPE, got ACTUAL
#define putln_error_arg_type(type, actual, action_name, key, ...) putln_error_json_type(type, actual, action_name ".with." key __VA_OPT__(, ) __VA_ARGS__)
// error: DO.with.key: invalid value: MSG
#define putln_error_arg_invalid(action_name, key, ...) put_error(action_name ".with." key ": invalid value\n" __VA_OPT__(, ) __VA_ARGS__)
#define putln_error_arg_invalid_because(action_name, key, reason, ...) put_error(action_name ".with." key ": invalid value: " reason "\n" __VA_OPT__(, ) __VA_ARGS__)

/// @return @ref serial_t The user ID.
/// @return @ref errstatus_handled An error occured and was handled.
/// @return @ref errstatus_error Invalid user key.
static inline serial_t get_user_id(char const *action_name, json_object *obj_with, char const *key, db_t *db) {
    json_object *obj_user;
    if (!json_object_object_get_ex(obj_with, key, &obj_user)) {
        putln_error_arg_missing("%s", "%s", action_name, key);
        return errstatus_handled;
    }
    switch (json_object_get_type(obj_user)) {
    case json_type_int: {
        serial_t maybe_user_id = json_object_get_int(obj_user);
        return maybe_user_id > 0 ? maybe_user_id : errstatus_error;
    }
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
/// @return @c true on success
/// @return @c false on error. @p out_str is untouches
static inline bool get_content(slice_t *out_str, char const *action_name, json_object *obj_with, char const *key) {
    json_object *obj_content;
    if (!json_object_object_get_ex(obj_with, key, &obj_content)) {
        putln_error_arg_missing("%s", "%s", action_name, key);
        return false;
    }
    if (!json_object_get_string_strict(obj_content, out_str)) {
        putln_error_arg_type(json_type_string, json_object_get_type(obj_content), "%s", "%s", action_name, key);
        return false;
    }
    return true;
}

/// @return a page number
/// @return @c 0 on error
static inline page_number_t get_page(char const *action_name, json_object *obj_with) {
    json_object *obj_page;
    if (!json_object_object_get_ex(obj_with, "page", &obj_page)) {
        putln_error_arg_missing("%s", "page", action_name);
        return 0;
    }
    page_number_t page;
    if (!json_object_get_int_strict(obj_page, &page)) {
        putln_error_arg_type(json_type_int, json_object_get_type(obj_page), "%s", "page", action_name);
        return 0;
    }
    return page;
}

/// @return a message ID
/// @return @c 0 on error
static inline serial_t get_msg_id(char const *action_name, json_object *obj_with) {
    json_object *obj_msg_id;
    if (!json_object_object_get_ex(obj_with, "msg_id", &obj_msg_id)) {
        putln_error_arg_missing("%s", "msg_id", action_name);
        return 0;
    }
    serial_t msg_id;
    if (!json_object_get_int_strict(obj_msg_id, &msg_id)) {
        putln_error_arg_type(json_type_int, json_object_get_type(obj_msg_id), "%s", "msg_id", action_name);
        return 0;
    }
    return msg_id;
}

/// @return a token
/// @return @c 0 on error
static inline token_t get_token(char const *action_name, json_object *obj_with) {
    json_object *obj_token;
    if (!json_object_object_get_ex(obj_with, "token", &obj_token)) {
        putln_error_arg_missing("%s", "token", action_name);
        return 0;
    }
    int64_t token;
    if (!json_object_get_int64_strict(obj_token, &token)) {
        putln_error_arg_type(json_type_int, json_object_get_type(obj_token), "%s", "token", action_name);
        return 0;
    }
    return (token_t)token;
}

static inline bool get_api_key(uuid4_t *out_api_key, char const *action_name, json_object *obj_with) {
    json_object *obj_api_key;
    if (!json_object_object_get_ex(obj_with, "api_key", &obj_api_key)) {
        putln_error_arg_missing("%s", "api_key", action_name);
        return false;
    }
    slice_t repr;
    if (!json_object_get_string_strict(obj_api_key, &repr)) {
        putln_error_arg_type(json_type_string, json_object_get_type(obj_api_key), "%s", "api_key", action_name);
        return false;
    }
    if (!uuid4_from_repr(out_api_key, repr.val)) {
        putln_error_arg_invalid_because("%s", "api_key", "invalid API key", action_name);
        return false;
    }
    return true;
}

action_t action_parse(json_object *obj, db_t *db) {
    action_t action = {};

#define fail()                           \
    do {                                 \
        action.type = action_type_error; \
        return action;                   \
    } while (0)

    json_object *obj_do;

    if (!json_object_object_get_ex(obj, "do", &obj_do)) {
        putln_error_json_missing_key("do", "action");
        fail();
    }

    slice_t action_name;
    if (!json_object_get_string_strict(obj_do, &action_name)) {
        putln_error_json_type(json_type_string, json_object_get_type(obj_do), "do");
        fail();
    }

    json_object *obj_with;
    if (!json_object_object_get_ex(obj, "with", &obj_with)) {
        putln_error_json_missing_key("with", "action");
        fail();
    }

#define action_is(name) streq(STR(name), action_name.val)

#define DO login
    if (action_is(DO)) {
        action.type = action_type(DO);

        // api_key
        if (!get_api_key(&action.with.DO.api_key, STR(DO), obj_with)) fail();

        // password
        // Check if key exists
        json_object *obj_password;
        if (!json_object_object_get_ex(obj_with, "password", &obj_password)) {
            putln_error_arg_missing("password", STR(DO));
            return action;
        }
        // Ensure value has correct type
        if (!json_object_get_string_strict(obj_password, &action.with.DO.password)) {
            putln_error_arg_type(json_type_string, json_object_get_type(obj_password), STR(DO), "password");
            return action;
        }
#undef DO
#define DO logout
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();
#undef DO
#define DO whois
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // api_key
        if (!get_api_key(&action.with.DO.api_key, STR(DO), obj_with)) fail();

        // user
        switch (action.with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: putln_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: fail();
        }
#undef DO
#define DO send
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();

        // content
        if (!get_content(&action.with.DO.content, STR(DO), obj_with, "content")) fail();

        // dest
        switch (action.with.DO.dest_user_id = get_user_id(STR(DO), obj_with, "dest", db)) {
        case errstatus_error: putln_error_arg_invalid(STR(DO), "dest"); [[fallthrough]];
        case errstatus_handled: fail();
        }
#undef DO
#define DO motd
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();
#undef DO
#define DO inbox
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();

        // page
        if (!(action.with.DO.page = get_page(STR(DO), obj_with))) fail();
#undef DO
#define DO outbox
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();

        // page
        if (!(action.with.DO.page = get_page(STR(DO), obj_with))) fail();
#undef DO
#define DO edit
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();

        // msg_id
        if (!(action.with.DO.msg_id = get_msg_id(STR(DO), obj_with))) fail();

        // new_content
        if (!get_content(&action.with.DO.new_content, STR(DO), obj_with, "new_content")) fail();
#undef DO
#define DO rm
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();

        // msg_id
        if (!(action.with.DO.msg_id = get_msg_id(STR(DO), obj_with))) fail();
#undef DO
#define DO block
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();

        // user
        switch (action.with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: putln_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: fail();
        }
#undef DO
#define DO unblock
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();

        // user
        switch (action.with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: putln_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: fail();
        }
#undef DO
#define DO ban
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();

        // user
        switch (action.with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: putln_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: fail();
        }
#undef DO
#define DO unban
    } else if (action_is(DO)) {
        action.type = action_type(DO);
        // token
        if (!(action.with.DO.token = get_token(STR(DO), obj_with))) fail();

        // user
        switch (action.with.DO.user_id = get_user_id(STR(DO), obj_with, "user", db)) {
        case errstatus_error: putln_error_arg_invalid(STR(DO), "user"); [[fallthrough]];
        case errstatus_handled: fail();
        }
    } else {
        put_error("unknown action: %s\n", action_name.val);
        fail();
    }

    return action;
}

json_object *response_to_json(response_t *response) {
    json_object *obj = json_object_new_object(), *obj_body = json_object_new_object();

#define add_key(o, k, v) json_object_object_add_ex(o, k, v, JSON_C_OBJECT_ADD_KEY_IS_NEW | JSON_C_OBJECT_KEY_IS_CONSTANT)

    add_key(obj, "status", json_object_new_int(response->status));
    add_key(obj, "has_next_page", json_object_new_boolean(response->has_next_page));
    add_key(obj, "body", obj_body);

    switch (response->status) {
    case status_unauthorized: break;
    case status_forbidden: break;
    case status_not_found: break;
    case status_payload_too_large: break;
    case status_unprocessable_content: break;
    case status_too_many_requests: break;
    case status_internal_server_error: break;
    case status_ok:
        switch (response->type) {
        case action_type_error: break;
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
    }
#undef add_key

    return obj;
}
