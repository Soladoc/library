/// @file
/// @author Raphaël
/// @brief Tchattator413 test - empty request
/// @date 1/02/2025

#include "tests_tchattator413.h"
#include <tchattator413/tchattator413.h>

#define NAME zero

#define IN "[]"
#define OUT "[]"

static void on_action(action_t const *action, void *t) {
    base_on_action(t);
    (void)action;
}

static void on_response(response_t const *response, void *t) {
    base_on_response(t);
    (void)response;
}

TEST_SIGNATURE(NAME) {
    test_t test = { .t = test_start(STR(NAME)) };

    json_object *obj_input = json_tokener_parse(IN);

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 0);

    test_case_o(&test, obj_output, OUT);

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
