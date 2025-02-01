/// @file
/// @author Raphaël
/// @brief Tchattator413 test - whois of 1 by invalid
/// @date 1/02/2025

#include <tchattator413/tchattator413.h>
#include "tests_tchattator413.h"

#define IN "[{\"do\":\"whois\",\"with\":{\"api_key\":\"" API_KEY_NONE1 "\",\"user\":1}}]"
#define OUT "[{\"status\":401,\"has_next_page\":false,\"body\":{}}]"

static void on_action(action_t const *action, void *t) {
    base_on_action(t);
    if (!test_case(t, action->type == action_type_whois, "type")) return;
    test_case(t, uuid4_eq_repr(action->with.whois.api_key, API_KEY_NONE1), "api key");
    test_case(t, action->with.whois.user_id == 1, "user id");
}

static void on_response(response_t const *response, void *t) {
    base_on_response(t);
    test_case(t, response->type == action_type_whois, "type");
    test_case(t, response->status == status_unauthorized, "status == %d", response->status);
    test_case(t, !response->has_next_page, "has next page");
}

struct test test_tchattator413_invalid_whois_1(cfg_t *cfg, db_t *db, server_t *server) {
    test_t test = { .t = test_start(__func__) };

    json_object *obj_input = json_tokener_parse(IN);
    test_case_i(test, obj_input, IN);

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 1);

    test_case_o(test, obj_output, OUT);

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}