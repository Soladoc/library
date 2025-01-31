#include "../src/tchattator413.h"
#include "../src/util.h"
#include "tests.h"
#include <json-c/json.h>

#define zero_I "[]"
#define zero_O "[]"

#define ADMIN_API_KEY_REPR "ed33c143-5752-4543-a821-00a187955a28"
#define ADMIN_API_KEY uuid4_of(0xed, 0x33, 0xc1, 0x43, 0x57, 0x52, 0x45, 0x43, 0xa8, 0x21, 0x00, 0xa1, 0x87, 0x95, 0x5a, 0x28)
#define INVALID_API_KEY_REPR "00123400-0000-0000-0100-000000000000"
#define INVALID_API_KEY uuid4_of(0x00, 0x12, 0x34, 0x00, 0x00, 0x00, 0x00, 0x00, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00)
typedef struct {
    /// @brief Backing test.
    struct test t;
    int n_actions, n_responses;
} test_t;

#define new_test() { .t = test_start(__func__) }

#define begin_on_action(ptest) \
    ++((test_t *)ptest)->n_actions

#define begin_on_response(ptest) \
    ++((test_t *)ptest)->n_responses

#define get_t(ptest) &((test_t *)ptest)->t;

#define test_case_count(t, actual, expected, singular) test_case(t, actual == expected, "expected %d %s%s, got %d", expected, singular, expected == 1 ? "" : "s", actual)
#define test_case_n_actions(test, expected)                               \
    do {                                                                  \
        test_case_count(&test.t, test.n_actions, expected, "action");     \
        test_case_count(&test.t, test.n_responses, expected, "response"); \
    } while (0)

static void zero_on_action(action_t const *action, void *test) {
    (void)action;
    begin_on_action(test);
}

static void zero_on_response(response_t const *response, void *test) {
    (void)response;
    begin_on_response(test);
}

struct test test_tchattator413_zero(cfg_t *cfg, db_t *db, server_t *server) {
    test_t test = new_test();

    json_object *obj_input = json_tokener_parse(zero_I);
    test_case(&test.t, obj_input, "parse input JSON successful");

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, zero_on_action, zero_on_response, &test);
    test_case_n_actions(test, 0);

    char const *json_output = json_object_to_json_string_ext(obj_output, JSON_C_TO_STRING_PLAIN);
    test_case(&test.t, streq(json_output, zero_O), "json output == expected");

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}

#define admin_whois_1_I "[{\"do\":\"whois\",\"with\":{\"api_key\":\"" ADMIN_API_KEY_REPR "\",\"user\":1}}]"
#define admin_whois_1_O "[{\"status\":200,\"has_next_page\":false,\"body\":{\"user_id\":1,\"email\":\"contact@mertrem.org\",\"last_name\":\"Dephric\",\"first_name\":\"Max\",\"display_name\":\"MERTREM Solutions\",\"kind\":1}}]"

static void admin_whois_1_on_action(action_t const *action, void *test) {
    begin_on_action(test);
    struct test *t = get_t(test);
    if (!test_case(t, action->type == action_type_whois, "action type")) return;
    test_case(t, uuid4_eq(action->with.whois.api_key, ADMIN_API_KEY), "api key");
    test_case(t, action->with.whois.user_id == 1, "user id");
}

static void admin_whois_1_on_response(response_t const *response, void *test) {
    begin_on_response(test);
    struct test *t = get_t(test);
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

    json_object *obj_input = json_tokener_parse(admin_whois_1_I);
    test_case(&test.t, obj_input, "parse input JSON successful");

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, admin_whois_1_on_action, admin_whois_1_on_response, &test);
    test_case_n_actions(test, 1);

    char const *json_output = json_object_to_json_string_ext(obj_output, JSON_C_TO_STRING_PLAIN);
    test_case(&test.t, streq(json_output, admin_whois_1_O), "json output == expected");

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}

#define invalid_whois_1_I "[{\"do\":\"whois\",\"with\":{\"api_key\":\"" INVALID_API_KEY_REPR "\",\"user\":1}}]"
#define invalid_whois_1_O "[{\"status\":401,\"has_next_page\":false,\"body\":{}}]"

static void invalid_whois_1_on_action(action_t const *action, void *test) {
    begin_on_action(test);
    struct test *t = get_t(test);
    if (!test_case(t, action->type == action_type_whois, "action type")) return;
    test_case(t, uuid4_eq(action->with.whois.api_key, INVALID_API_KEY), "api key");
    test_case(t, action->with.whois.user_id == 1, "user id");
}

static void invalid_whois_1_on_response(response_t const *response, void *test) {
    begin_on_response(test);
    struct test *t = get_t(test);
    test_case(t, response->type == action_type_whois, "action type");
    test_case(t, response->status == status_unauthorized, "status");
    test_case(t, !response->has_next_page, "has next page");
}

struct test test_tchattator413_invalid_whois_1(cfg_t *cfg, db_t *db, server_t *server) {
    test_t test = new_test();

    json_object *obj_input = json_tokener_parse(invalid_whois_1_I);
    test_case(&test.t, obj_input, "parse input JSON successful");

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, invalid_whois_1_on_action, invalid_whois_1_on_response, &test);
    test_case_n_actions(test, 1);

    char const *json_output = json_object_to_json_string_ext(obj_output, JSON_C_TO_STRING_PLAIN);
    test_case(&test.t, streq(json_output, invalid_whois_1_O), "json output == expected");

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}
