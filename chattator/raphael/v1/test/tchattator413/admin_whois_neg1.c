#include <tchattator413/tchattator413.h>
#include "tests_tchattator413.h"
#include <json-c/json.h>

#define IN "[{\"do\":\"whois\",\"with\":{\"api_key\":\"" ADMIN_API_KEY_REPR "\",\"user\":-1}}]"
#define OUT OUTPUT_500

static void admin_whois_neg1_on_action(action_t const *action, void *t) {
    begin_on_action(t);
    if (!test_case(t, action->type == action_type_error, "action type")) return;
}

static void admin_whois_neg1_on_response(response_t const *response, void *t) {
    begin_on_response(t);
    test_case(t, response->type == action_type_error, "action type");
    test_case(t, response->status == status_internal_server_error, "status");
}

struct test test_tchattator413_admin_whois_neg1(cfg_t *cfg, db_t *db, server_t *server) {
    test_t test = new_test();

    json_object *obj_input = json_tokener_parse(IN);
    test_case_i(test, obj_input, IN);

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, admin_whois_neg1_on_action, admin_whois_neg1_on_response, &test);
    test_case_n_actions(test, 1);

    test_case_o(test, obj_output, OUT);

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}