/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Implementation (JSON-related)
/// @date 23/01/2025

#include <json-c/json.h>
#include <stddef.h>
#include <tchattator413/action.h>
#include <tchattator413/errstatus.h>
#include <tchattator413/json-helpers.h>
#include <tchattator413/util.h>

#if __STDC_VERSION__ < 202000L
#define unreachable()
#endif

/// @return @ref serial_t The user ID.
/// @return @ref errstatus_handled An error occured and was handled.
/// @return @ref errstatus_error Invalid user key.
static inline serial_t get_user_id(json_object *obj_user, db_t *db) {
    switch (json_object_get_type(obj_user)) {
    case json_type_int: {
        serial_t maybe_user_id = json_object_get_int(obj_user);
        return maybe_user_id > 0 ? maybe_user_id : errstatus_error;
    }
    case json_type_string: {
        if (json_object_get_string_len(obj_user) > MAX(EMAIL_LENGTH, PSEUDO_LENGTH)) break;
        const char *email_or_pseudo = json_object_get_string(obj_user);
        return strchr(email_or_pseudo, '@')
            ? db_get_user_id_by_email(db, email_or_pseudo)
            : db_get_user_id_by_pseudo(db, email_or_pseudo);
    }
    default:;
    }
    return errstatus_error;
}

action_t action_parse(json_object *obj, db_t *db) {
    action_t action = {};

#define fail()                                                  \
    do {                                                        \
        action.type = action_type_error;                        \
        action.with.error.type = action_error_type_unspecified; \
        return action;                                          \
    } while (0)

#define fail_missing_key(_location)                              \
    do {                                                         \
        action.type = action_type_error;                         \
        action.with.error.type = action_error_type_missing_key;  \
        action.with.error.info.missing_key.location = _location; \
        return action;                                           \
    } while (0)

#define fail_type(_location, _obj_actual, _expected)          \
    do {                                                      \
        action.type = action_type_error;                      \
        action.with.error.type = action_error_type_type;      \
        action.with.error.info.type.location = _location;     \
        action.with.error.info.type.obj_actual = _obj_actual; \
        action.with.error.info.type.expected = _expected;     \
        return action;                                        \
    } while (0)

#define fail_invalid(_location, _obj_bad, _reason)           \
    do {                                                     \
        action.type = action_type_error;                     \
        action.with.error.type = action_error_type_invalid;  \
        action.with.error.info.invalid.location = _location; \
        action.with.error.info.invalid.obj_bad = _obj_bad;   \
        action.with.error.info.invalid.reason = _reason;     \
        return action;                                       \
    } while (0)

#define getarg(obj, key, out_value, json_type, getter)         \
    do {                                                       \
        if (!json_object_object_get_ex(obj_with, key, &obj)) { \
            fail_missing_key(arg_loc(key));                    \
        }                                                      \
        if (!getter(obj, out_value)) {                         \
            fail_type(arg_loc(key), obj, json_type);           \
        }                                                      \
    } while (0)

#define getarg_string(obj, key, out_value) getarg(obj, key, out_value, json_type_string, json_object_get_string_strict)
#define getarg_int(obj, key, out_value) getarg(obj, key, out_value, json_type_int, json_object_get_int_strict)
#define getarg_int64(obj, key, out_value) getarg(obj, key, out_value, json_type_int, json_object_get_int64_strict)
#define getarg_user(obj, key, out_value)                                           \
    do {                                                                           \
        if (!json_object_object_get_ex(obj_with, key, &obj)) {                     \
            fail_missing_key(arg_loc(key));                                        \
        }                                                                          \
        switch (*out_value = get_user_id(obj, db)) {                               \
        case errstatus_error: fail_invalid(arg_loc(key), obj, "invalid user key"); \
        case errstatus_handled: fail();                                            \
        case errstatus_ok:;                                                        \
        }                                                                          \
    } while (0)
#define getarg_page(obj, key, out_value)                            \
    do {                                                            \
        getarg_int(obj, key, out_value);                            \
        if (*out_value < 1) {                                       \
            fail_invalid(arg_loc(key), obj, "invalid page number"); \
        }                                                           \
    } while (0)
#define getarg_api_key(obj, get, out_value)                              \
    do {                                                                 \
        slice_t api_key_repr;                                            \
        getarg_string(obj, "api_key", &api_key_repr);                    \
        if (!uuid4_parse_slice(&action.with.DO.api_key, api_key_repr)) { \
            fail_invalid(arg_loc("api_key"), obj, "invalid API key");    \
        }                                                                \
    } while (0)

#define arg_loc(key) (STR(DO) ".with" key)

    json_object *obj_do;
    if (!json_object_object_get_ex(obj, "do", &obj_do)) fail_missing_key("action.do");

    slice_t action_name;
    if (!json_object_get_string_strict(obj_do, &action_name)) fail_type("action", obj_do, json_type_string);

    json_object *obj_with;
    if (!json_object_object_get_ex(obj, "with", &obj_with)) fail_missing_key("action.with");

// this is save because the null terminator of the literal string STR(name) will stop strcmp
#define action_is(name) streq(STR(name), action_name.val)

#define DO login
    if (action_is(DO)) {
        action.type = action_type(DO);

        // api_key
        json_object *obj_api_key;
        getarg_api_key(obj_api_key, "api_key", &action.with.DO.api_key);

        // password
        json_object *obj_password;
        getarg_string(obj_password, "password", &action.with.DO.password);
#undef DO
#define DO logout
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);
#undef DO
#define DO whois
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // api_key
        json_object *obj_api_key;
        getarg_api_key(obj_api_key, "api_key", &action.with.DO.api_key);

        // user
        json_object *obj_user;
        getarg_user(obj_user, "user", &action.with.DO.user_id);
#undef DO
#define DO send
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);

        // content
        json_object *obj_content;
        getarg_string(obj_content, "content", &action.with.DO.content);

        // dest
        json_object *obj_dest;
        getarg_user(obj_dest, "dest", &action.with.DO.dest_user_id);
#undef DO
#define DO motd
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);
#undef DO
#define DO inbox
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);

        // page
        json_object *obj_page;
        getarg_page(obj_page, "page", &action.with.DO.page);
#undef DO
#define DO outbox
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);

        // page
        json_object *obj_page;
        getarg_page(obj_page, "page", &action.with.DO.page);
#undef DO
#define DO edit
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);

        // msg_id
        json_object *obj_msg_id;
        getarg_int(obj_msg_id, "msg_id", &action.with.DO.msg_id);

        // new_content
        json_object *obj_new_content;
        getarg_string(obj_new_content, "new_content", &action.with.DO.new_content);
#undef DO
#define DO rm
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);

        // msg_id
        json_object *obj_msg_id;
        getarg_int(obj_msg_id, "msg_id", &action.with.DO.msg_id);
#undef DO
#define DO block
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);

        // user
        json_object *obj_user;
        getarg_user(obj_user, "user", &action.with.DO.user_id);
#undef DO
#define DO unblock
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);

        // user
        json_object *obj_user;
        getarg_user(obj_user, "user", &action.with.DO.user_id);
#undef DO
#define DO ban
    } else if (action_is(DO)) {
        action.type = action_type(DO);

        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);

        // user
        json_object *obj_user;
        getarg_user(obj_user, "user", &action.with.DO.user_id);
#undef DO
#define DO unban
    } else if (action_is(DO)) {
        action.type = action_type(DO);
        // token
        json_object *obj_token;
        getarg_int64(obj_token, "token", &action.with.DO.token);

        // user
        json_object *obj_user;
        getarg_user(obj_user, "user", &action.with.DO.user_id);
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
    // error: DO.with: missing key: KEY
    // #define putln_error_arg_missing(action_name, key, ...) putln_error_json_missing_key(key, action_name ".with" __VA_OPT__(, ) __VA_ARGS__)
    // error: DO.with.key: type: expected TYPE, got ACTUAL
    // #define putln_error_arg_type(type, actual, action_name, key, ...) putln_error_json_type(type, actual, action_name ".with." key __VA_OPT__(, ) __VA_ARGS__)
    // error: DO.with.key: invalid value: MSG
    // #define putln_error_arg_invalid(action_name, key, reason, ...) put_error(action_name ".with." key ": invalid value: " reason "\n" __VA_OPT__(, ) __VA_ARGS__)

    if (response->type == action_type_error && response->body.error.type != action_error_type_unspecified) {
        char *msg;
        switch (response->body.error.type) {
        case action_error_type_type: {
            char const *fmt = "%s: expected %s, got %s";
            char const *arg1 = response->body.error.info.type.location;
            char const *arg2 = json_type_to_name(response->body.error.info.type.expected);
            char const *arg3 = json_type_to_name(json_object_get_type(response->body.error.info.type.obj_actual));
            msg = malloc(buffer_size(fmt, arg1, arg2, arg3));
            sprintf(msg, fmt, arg1, arg2, arg3);
            break;
        }
        case action_error_type_missing_key: {
            char const *fmt = "%s: key missing";
            char const *arg1 = response->body.error.info.missing_key.location;
            msg = malloc(buffer_size(fmt, arg1));
            sprintf(msg, fmt, arg1);
            break;
        }
        case action_error_type_invalid: {
            char const *fmt = "%s: %s: %s";
            char const *arg1 = response->body.error.info.invalid.location;
            char const *arg2 = response->body.error.info.invalid.reason;
            char const *arg3 = json_object_to_json_string(response->body.error.info.invalid.obj_bad);
            msg = malloc(buffer_size(fmt, arg1, arg2, arg3));
            sprintf(msg, fmt, arg1, arg2, arg3);
            break;
        }
        default: unreachable();
        }
        add_key(obj_body, "message", json_object_new_string(COALESCE(msg, "(out of memory)")));
        free(msg);
    } else if (response->status == status_ok) {
        switch (response->type) {
        case action_type_login:
            add_key(obj_body, "token", json_object_new_int64(response->body.login.token));
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
        default: unreachable();
        }
    }
#undef add_key

    return obj;
}
