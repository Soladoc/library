#include "../src/tchattator413.h"
#include "../src/util.h"
#include "tests.h"
#include <json-c/json.h>

#define T1_A "[]"
#define T1_D "[]"

static void test_1_on_action(action_t const *action, void *t)
{
    (void) action;
    test_case(t, false, "was not suppoed to be any actions");
}

static void test_1_on_response(response_t const *response, void *t)
{
    (void) response;
    test_case(t, false, "was not suppoed to be any responses");
}

struct test test_1(cfg_t *cfg, db_t *db, server_t *server) {
    struct test t = test_start("test_1");

    json_object *obj_input = json_tokener_parse(T1_A);
    test_case(&t, obj_input, "parse input JSON success");

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, test_1_on_action, test_1_on_response, &t);

    char const *json_output = json_object_to_json_string_ext(obj_output, JSON_C_TO_STRING_PLAIN);
    test_case(&t, streq(json_output, T1_D), "json output == expected");

    json_object_put(obj_output);
    json_object_put(obj_input);

    return t;
}

// test_case(&t, action_parse(&a, obj_a, db), "action_parse");