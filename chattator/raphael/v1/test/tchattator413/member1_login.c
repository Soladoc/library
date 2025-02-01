/// @file
/// @author Raphaël
/// @brief Tchattator413 test - login by member 1
/// @date 1/02/2025

#include "tests_tchattator413.h"
#include <tchattator413/tchattator413.h>

#define IN "{\"do\":\"login\",\"with\":{\"api_key\":\"" API_KEY_MEMBER1 "\",\"password\":\"member1_mdp\"}}"
// we can't include the whole output because the actual token value unspecified and determined at runtime
#define OUT_START "[{\"status\":200,\"has_next_page\":false,\"body\":{\"token\":"

static void on_action(action_t const *action, void *t) {
    base_on_action(t);
    if (!test_case(t, action->type == action_type_login, "type")) return;
    test_case(t, uuid4_eq_repr(action->with.login.api_key, API_KEY_MEMBER1), "api key");
    test_case(t, streq(action->with.login.password.val, "member1_mdp"), "password");
}

static void on_response(response_t const *response, void *t) {
    test_t *test = base_on_response(t);
    if (!test_case(t, response->type == action_type_login, "type")) return;
    test_case(t, !response->has_next_page, "has next page");
    test_case(t, response->status == status_ok, "status == %d", response->status);
    test_case(t, server_verify_token(test->server, response->body.login.token), "server verifies token");
}

struct test test_tchattator413_member1_login(cfg_t *cfg, db_t *db, server_t *server) {
    test_t test = {
        .t = test_start(__func__),
        .server = server,
    };

    json_object *obj_input = json_tokener_parse(IN);
    test_case_i(test, obj_input, IN);

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 1);

    test_case_o_start(test, obj_output, OUT_START);

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
