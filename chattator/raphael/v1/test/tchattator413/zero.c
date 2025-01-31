#include "tests_tchattator413.h"
#include <json-c/json.h>
#include <tchattator413/tchattator413.h>

#define IN "[]"
#define OUT "[]"

static void zero_on_action(action_t const *action, void *test) {
    (void)action;
    begin_on_action(test);
}

static void zero_on_response(response_t const *response, void *test) {
    (void)response;
    begin_on_response(test);
}

struct test test_tchattator413_zero(cfg_t *cfg, db_t *db, server_t *server) {
    test_t test = new_test();

    json_object *obj_input = json_tokener_parse(IN);
    test_case_i(test, obj_input, IN);

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, zero_on_action, zero_on_response, &test);
    test_case_n_actions(test, 0);

    test_case_o(test, obj_output, OUT);

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
