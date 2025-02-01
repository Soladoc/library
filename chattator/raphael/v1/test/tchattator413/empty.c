/// @file
/// @author Raphaël
/// @brief Tchattator413 test - empty request
/// @date 1/02/2025

#include "tests_tchattator413.h"
#include <tchattator413/tchattator413.h>

#define IN ""
#define OUT "[{\"status\":500,\"has_next_page\":false,\"body\":{\"message\":\"request: expected object, got null\"}}]"

static void on_action(action_t const *action, void *t) {
    base_on_action(t);
    (void)action;
}

static void on_response(response_t const *response, void *t) {
    base_on_response(t);
    (void)response;
}

struct test test_tchattator413_empty(cfg_t *cfg, db_t *db, server_t *server) {
    test_t test = { .t = test_start(__func__) };

    json_object *obj_input = json_tokener_parse(IN);
    test_case(&test.t, !obj_input, "input failed to parse");

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 0);

    test_case_o(test, obj_output, OUT);

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
