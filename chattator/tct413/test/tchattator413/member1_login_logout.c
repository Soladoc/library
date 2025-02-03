/// @file
/// @author Raphaël
/// @brief Tchattator413 test - member1 logs in and logs out
/// @date 1/02/2025

#include "tests_tchattator413.h"
#include <tchattator413/tchattator413.h>

#define NAME member1_login_logout

static void on_action_1(action_t const *action, void *t) {
    base_on_action(t);
    if (!test_case_eq_int(t, action->type, action_type_login, )) return;
    test_case_eq_uuid(t, action->with.login.api_key, API_KEY_MEMBER1, );
    test_case_eq_str(t, action->with.login.password.val, "member1_mdp", );
}

static void on_response_1(response_t const *response, void *t) {
    test_t *test = base_on_response(t);
    test_case(t, !response->has_next_page, "");
    if (!test_case_eq_int(t, response->type, action_type_login, )) return;
    test_case(t, -1 != server_verify_token(test->server, response->body.login.token), "server verifies token %ld", response->body.login.token);
}

static token_t gs_token;

static void on_action_2(action_t const *action, void *t) {
    base_on_action(t);
    if (!test_case_eq_int(t, action->type, action_type_logout, )) return;
    test_case_eq_int64(t, action->with.logout.token, gs_token, );
}

static void on_response_2(response_t const *response, void *t) {
    base_on_response(t);
    test_case_eq_int(t, response->type, action_type_logout, );
}

TEST_SIGNATURE(NAME) {
    test_t test = {
        .t = test_start(STR(NAME)),
        .server = server,
    };

    json_object *obj_input = json_object_from_file(IN_FILE(NAME, "1"));
    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action_1, on_response_1, &test);
    test_case_n_actions(&test, 1);
    bool ok = test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, "1"), &gs_token);
    json_object_put(obj_input);
    json_object_put(obj_output);
    if (!ok) return test.t;

    obj_input = input_file_fmt(IN_FILE(NAME, "2"), gs_token);
    obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action_2, on_response_2, &test);
    test_case_n_actions(&test, 2);
    test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, "2"));
    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
