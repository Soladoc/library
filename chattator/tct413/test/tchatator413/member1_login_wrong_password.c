/// @file
/// @author Raphaël
/// @brief Tchatator413 test - login by member 1, wrong password
/// @date 1/02/2025

#include "tests_tchatator413.h"
#include <tchatator413/tchatator413.h>

#define NAME member1_login_wrong_password

static void on_action(action_t const *action, void *t) {
    base_on_action(t);
    if (!test_case_eq_int(t, action->type, action_type_login, )) return;
    test_case_eq_uuid(t, action->with.login.api_key, API_KEY_MEMBER1, );
    test_case_eq_str(t, action->with.login.password.val, "member1_mdp_suffix_that_makes_it_wrong", );
}

static void on_response(response_t const *response, void *t) {
    base_on_response(t);
    test_case(t, !response->has_next_page, "");
    if (!test_case_eq_int(t, response->type, action_type_error, )) return;
    if (!test_case_eq_int(t, response->body.error.type, action_error_type_other, )) return;
    test_case_eq_int(t, response->body.error.info.other.status, status_forbidden, );
}

TEST_SIGNATURE(NAME) {
    test_t test = {
        .t = test_start(STR(NAME)),
        .server = server,
    };

    json_object *obj_input = load_input_json(IN_JSON(NAME, ));

    json_object *obj_output = tchatator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 1);

    test_case_o_file_fmt(&test, obj_output, OUT_JSON(NAME, ));

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
