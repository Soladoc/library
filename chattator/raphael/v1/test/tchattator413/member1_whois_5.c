/// @file
/// @author Raphaël
/// @brief Tchattator413 test - whois of 1 by admin
/// @date 1/02/2025

#include <tchattator413/tchattator413.h>
#include "tests_tchattator413.h"

#define NAME member1_whois_5

static void on_action(action_t const *action, void *t) {
    base_on_action(t);
    if (!test_case_eq_int(t, action->type, action_type_whois, "type")) return;
    test_case_eq_uuid(t, action->with.whois.api_key, API_KEY_MEMBER1, "api key");
    test_case_eq_int(t, action->with.whois.user_id, 5, "user id");
}

static void on_response(response_t const *response, void *t) {
    base_on_response(t);
    if (!test_case_eq_int(t, response->type, action_type_whois, "type")) return;
    test_case(t, !response->has_next_page, "");
    test_case_eq_int(t, response->body.whois.user_id, 5, "user id");
    test_case_eq_int(t, response->body.whois.kind, user_kind_membre, "kind");
    test_case_eq_str(t, response->body.whois.display_name, "member1", "display name");
    test_case_eq_str(t, response->body.whois.email, "member@1.413", "email");
    test_case_eq_str(t, response->body.whois.first_name, "member1_prenom", "first name");
    test_case_eq_str(t, response->body.whois.last_name, "member1_nom", "last name");
}

TEST_SIGNATURE(NAME) {
    test_t test = { .t = test_start(STR(NAME)) };

    json_object *obj_input = json_object_from_file(IN_FILE(NAME, ));

    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 1);

    test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, ));

    json_object_put(obj_output);
    json_object_put(obj_input);

    return test.t;
}