#include "../src/tchattator413.h"
#include "../src/util.h"
#include "tests.h"
#include <json-c/json.h>

#define zero_A "[]"
#define zero_D "[]"

#define ADMIN_API_KEY "ed33c143-5752-4543-a821-00a187955a28"

static const uuid4_t gs_admin_api_key = uuid4_init(0xed, 0x33, 0xc1, 0x43, 0x57, 0x52, 0x45, 0x43, 0xa8, 0x21, 0x00, 0xa1, 0x87, 0x95, 0x5a, 0x28);

static void zero_on_action(action_t const *action, void *t) {
    (void)action;
    test_case(t, false, "was not suppoed to be any actions");
}

static void zero_on_response(response_t const *response, void *t) {
    (void)response;
    test_case(t, false, "was not suppoed to be any responses");
}

struct test test_tchattator413_zero(cfg_t *cfg, db_t *db, server_t *server) {
    struct test t = test_start(__func__);

    json_object *obj_input = json_tokener_parse(zero_A);
    test_case(&t, obj_input, "parse input JSON successful");

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, zero_on_action, zero_on_response, &t);

    char const *json_output = json_object_to_json_string_ext(obj_output, JSON_C_TO_STRING_PLAIN);
    test_case(&t, streq(json_output, zero_D), "json output == expected");

    json_object_put(obj_output);
    json_object_put(obj_input);

    return t;
}

#define admin_whois_1_A "[{\"do\":\"whois\",\"with\":{\"api_key\":\"" ADMIN_API_KEY "\",\"user\":1}}]"
#define admin_whois_1_D "[{\"status\":200,\"has_next_page\":false,\"body\":{\"user_id\":1,\"email\":\"contact@mertrem.org\",\"last_name\":\"Dephric\",\"first_name\":\"Max\",\"display_name\":\"MERTREM Solutions\",\"kind\":1}}]"
// test_case(&t, action_parse(&a, obj_a, db), "action_parse");

static void admin_whois_1_on_action(action_t const *action, void *t) {
    if (!test_case(t, action->type == action_type_whois, "action type")) return;
    test_case(t, uuid4_eq(action->with.whois.api_key, gs_admin_api_key), "api key");
    test_case(t, action->with.whois.user_id == 1, "user id");
}

static void admin_whois_1_on_response(response_t const *response, void *t) {
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
    struct test t = test_start(__func__);

    json_object *obj_input = json_tokener_parse(admin_whois_1_A);
    test_case(&t, obj_input, "parse input JSON successful");

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, admin_whois_1_on_action, admin_whois_1_on_response, &t);

    char const *json_output = json_object_to_json_string_ext(obj_output, JSON_C_TO_STRING_PLAIN);
    test_case(&t, streq(json_output, admin_whois_1_D), "json output == expected");

    json_object_put(obj_output);
    json_object_put(obj_input);

    return t;
}
