#include "action.h"
#include "util.h"

#define fail(...)                     \
    do {                              \
        fprintf(stderr, __VA_ARGS__); \
        return false;                 \
    } while (0)

#define fail_missing(key) fail("error: missing key: " key "\n")

#define fail_missing_or_invalid(key) fail("error: missing key or invalid value: " key "\n")

bool action_parse(struct action *action, json_object *obj) {
    char const *name = json_object_get_string(json_object_object_get(obj, "do"));
    if (!name) fail_missing_or_invalid("do");

    json_object *with = json_object_object_get(obj, "with");

    // todo..

    if (streq(name, "login")) {
        char const *api_key = json_object_get_string(json_object_object_get(with, "api_key"));
        if (!api_key) fail_missing_or_invalid("api_key");
        uuid4_from_repr(&action->login.api_key, api_key);
    } else if (streq(name, "logout")) {

    } else if (streq(name, "whois")) {

    } else if (streq(name, "send")) {

    } else if (streq(name, "motd")) {

    } else if (streq(name, "inbox")) {

    } else if (streq(name, "outbox")) {

    } else if (streq(name, "edit")) {

    } else if (streq(name, "rm")) {

    } else if (streq(name, "block")) {

    } else if (streq(name, "unblock")) {

    } else if (streq(name, "ban")) {

    } else {
        return false;
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
