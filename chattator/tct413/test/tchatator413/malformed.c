/// @file
/// @author Raphaël
/// @brief Tchatator413 test - maformed request
/// @date 1/02/2025

#include "tests_tchatator413.h"
#include <tchatator413/tchatator413.h>

#define NAME malformed

#define IN "["

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
    test_case(&test.t, !obj_input, "input failed to parse");

    json_object *obj_output = tchatator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 0);

    test_case_o_file_fmt(&test, obj_output, OUT_JSON(NAME, ));

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
