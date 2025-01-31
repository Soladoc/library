#include <tchattator413/tchattator413.h>
#include "tests_tchattator413.h"
#include <json-c/json.h>

#define IN "[{\"do\":\"whois\",\"with\":{\"api_key\":\"" ADMIN_API_KEY_REPR "\",\"user\":1}}]"
#define OUT "[{\"status\":200,\"has_next_page\":false,\"body\":{\"user_id\":1,\"email\":\"contact@mertrem.org\",\"last_name\":\"Dephric\",\"first_name\":\"Max\",\"display_name\":\"MERTREM Solutions\",\"kind\":1}}]"

static void admin_whois_1_on_action(action_t const *action, void *t) {
    begin_on_action(t);
    if (!test_case(t, action->type == action_type_whois, "action type")) return;
    test_case(t, uuid4_eq(action->with.whois.api_key, ADMIN_API_KEY), "api key");
    test_case(t, action->with.whois.user_id == 1, "user id");
}

static void admin_whois_1_on_response(response_t const *response, void *t) {
    begin_on_response(t);
    if (!test_case(t, response->type == action_type_whois, "action type")) return;
    if (!test_case(t, response->status == status_ok, "status")) return;
    test_case(t, !response->has_next_page, "has next page");
    test_case(t, response->body.whois.user_id == 1, "user id");
    test_case(t, response->body.whois.kind == user_kind_pro_prive, "kind");
    test_case(t, streq(response->body.whois.display_name, "MERTREM Solutions"), "display name");
    test_case(t, streq(response->body.whois.email, "contact@mertrem.org"), "email");
    test_case(t, streq(response->body.whois.first_name, "Max"), "first name");
    test_case(t, streq(response->body.whois.last_name, "Dephric"), "last name");
}

struct test test_tchattator413_admin_whois_1(cfg_t *cfg, db_t *db, server_t *server) {
    test_t test = new_test();

    json_object *obj_input = json_tokener_parse(IN);
    test_case_i(test, obj_input, IN);

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, admin_whois_1_on_action, admin_whois_1_on_response, &test);
    test_case_n_actions(test, 1);

    test_case_o(test, obj_output, OUT);

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}