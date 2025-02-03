/// @file
/// @author Raphaël
/// @brief Tchatator413 test - inbox by pro1
/// @date 1/02/2025

#include "tests_tchatator413.h"
#include <tchatator413/action.h>
#include <tchatator413/tchatator413.h>

#define NAME pro1_inbox

#define MSG_CONTENT "Bonjour pro_1 (1er message)"

static token_t gs_token_pro1;

static void on_action(action_t const *action, void *t) {
    test_t const *test = base_on_action(t);
    switch (test->n_actions) {
    case 1: // inbox
        if (!test_case_eq_int(t, action->type, action_type_inbox, )) return;
        test_case_eq_int64(t, action->with.send.token, gs_token_pro1, );
        break;
    default: test_fail(t, "wrong test->n_actions: %d", test->n_actions);
    }
}

static void on_response(response_t const *response, void *t) {
    test_t const *test = base_on_response(t);
    test_case(t, !response->has_next_page, "");
    switch (test->n_responses) {
    case 1: { // inbox
        if (!test_case_eq_int(t, response->type, action_type_inbox, )) return;
        test_case_eq_int64(t, response->body.inbox.n_msgs, 1, );
        msg_t msg = response->body.inbox.msgs[0];
        test_case_eq_int(t, msg.id, 1, );
        test_case_eq_int(t, msg.read_age, 0, );
        test_case_eq_int(t, msg.edited_age, 0, );
        test_case_eq_int(t, msg.deleted_age, 0, );
        test_case_eq_int(t, msg.user_id_sender, 5, );
        test_case_eq_int(t, msg.user_id_recipient, 1, );
        test_case_eq_str(t, msg.content, MSG_CONTENT, );
        break;
    }
    default: test_fail(t, "wrong test->n_responses: %d", test->n_actions);
    }
}

TEST_SIGNATURE(NAME) {
    test_t test = {
        .t = test_start(STR(NAME)),
        .server = server,
        .db = db,
    };

    uuid4_t api_key_p1;
    assert(uuid4_parse(&api_key_p1, API_KEY_PRO1));

    // Pro logs in
    response_t response = action_evaluate(
        &(action_t) {
            .type = action_type_login,
            .with.login = {
                .api_key = api_key_p1,
                .password = SLICE_CONST("pro1_mdp"),
            },
        },
        cfg, db, server);
    bool ok = test_case_eq_int(&test.t, response.type, action_type_login, "pro logs in");
    gs_token_pro1 = response.body.login.token;
    response_destroy(&response);
    if (!ok) return test.t;

    // Pro queries inbox
    json_object *obj_input = input_file_fmt(IN_FILE(NAME, "_inbox"), gs_token_pro1);
    json_object *obj_output = tchatator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 1);
    test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, "_inbox"));

    json_object_put(obj_input);
    json_object_put(obj_output);

    // Pro logs out
    response = action_evaluate(
        &(action_t) {
            .type = action_type_logout,
            .with.logout.token = gs_token_pro1 },
        cfg, db, server);
    test_case_eq_int(&test.t, response.type, action_type_logout, "pro logs out");
    response_destroy(&response);

    return test.t;
}
