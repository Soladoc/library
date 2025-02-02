/// @file
/// @author Raphaël
/// @brief Tchattator413 test - login by member 1
/// @date 1/02/2025

#include "tests_tchattator413.h"
#include <tchattator413/tchattator413.h>

#define NAME member1_login_member2_login_member1_logout_member2_logout

static void on_action_login(action_t const *action, void *t) {
    test_t *test = base_on_action(t);
    if (!test_case_eq_int(t, action->type, action_type_login, )) return;
    switch (test->n_actions) {
    case 1:
        test_case_eq_uuid(t, action->with.login.api_key, API_KEY_MEMBER1, );
        test_case_eq_str(t, action->with.login.password.val, "member1_mdp", );
        break;
    case 2:
        test_case_eq_uuid(t, action->with.login.api_key, API_KEY_MEMBER2, );
        test_case_eq_str(t, action->with.login.password.val, "member2_mdp", );
        break;
    default:
        test_fail(t, "wrong test->n_actions: %d", test->n_actions);
    }
}

static void on_response_login(response_t const *response, void *t) {
    test_t *test = base_on_response(t);
    test_case(t, !response->has_next_page, "");
    if (!test_case_eq_int(t, response->type, action_type_login, )) return;
    test_case(t, -1 != server_verify_token(test->server, response->body.login.token), "server verifies token %ld", response->body.login.token);
}

static token_t gs_tokens[2];

static void on_action_logout(action_t const *action, void *t) {
    test_t *test = base_on_action(t);
    if (!test_case_eq_int(t, action->type, action_type_logout, )) return;
    test_case_eq_long(t, action->with.logout.token, gs_tokens[test->n_actions - 3], );
}

static void on_response_logout(response_t const *response, void *t) {
    base_on_response(t);
    test_case_eq_int(t, response->type, action_type_logout, );
}

TEST_SIGNATURE(NAME) {
    test_t test = {
        .t = test_start(STR(NAME)),
        .server = server,
    };

#define STOP()                       \
    do {                             \
        json_object_put(obj_input);  \
        json_object_put(obj_output); \
        return test.t;               \
    } while (0)

    json_object *obj_input = json_object_from_file(IN_FILE(NAME, "1"));
    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action_login, on_response_login, &test);
    test_case_n_actions(&test, 2);
    if (!test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, "1"), &gs_tokens[0], &gs_tokens[1])) STOP();

    json_object_put(obj_input);
    obj_input = input_file_fmt(IN_FILE(NAME, "2"), gs_tokens[0], gs_tokens[1]);
    json_object_put(obj_output);
    obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action_logout, on_response_logout, &test);
    test_case_n_actions(&test, 4);
    test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, "2"));

    STOP();
}
