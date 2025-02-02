/// @file
/// @author Raphaël
/// @brief Tchattator413 test - login by member 1
/// @date 1/02/2025

#include "tests_tchattator413.h"
#include <tchattator413/action.h>
#include <tchattator413/tchattator413.h>

#define NAME member1_send_pro1_inbox_member1_rm

#define MSG_CONTENT "Bonjour du language C :)"

static token_t gs_token_member1;
static token_t gs_token_pro1;
static serial_t gs_msg_id;
static time_t gs_msg_sent_at;

static void on_action(action_t const *action, void *t) {
    test_t const *test = base_on_action(t);
    switch (test->n_actions) {
    case 1: // send
        if (!test_case_eq_int(t, action->type, action_type_send, )) return;
        test_case_eq_long(t, action->with.send.token, gs_token_member1, );
        test_case_eq_str(t, action->with.send.content.val, MSG_CONTENT, );
        test_case_eq_int(t, action->with.send.dest_user_id, 1, );
        break;
    case 2: // inbox
        if (!test_case_eq_int(t, action->type, action_type_inbox, )) return;
        test_case_eq_long(t, action->with.inbox.token, gs_token_pro1, );
        test_case_eq_int(t, action->with.inbox.page, 1, );
        break;
    case 3: // rm
        if (!test_case_eq_int(t, action->type, action_type_rm, )) return;
        test_case_eq_long(t, action->with.rm.token, gs_token_member1, );
        test_case_eq_int(t, action->with.rm.msg_id, gs_msg_id, );
        break;
    default: test_fail(t, "wrong test->n_actions: %d", test->n_actions);
    }
}

static void on_response(response_t const *response, void *t) {
    test_t const *test = base_on_response(t);
    test_case(t, !response->has_next_page, "");
    switch (test->n_responses) {
    case 1: { // send
        if (!test_case_eq_int(t, response->type, action_type_send, )) return;
        void *memory_owner_db;
        msg_t msg = { .id = response->body.send.msg_id };
        if (!test_case(t, errstatus_ok == db_get_msg(test->db, &msg, &memory_owner_db), "sent msg id %d exists", msg.id)) return;
        gs_msg_id = msg.id;
        gs_msg_sent_at = msg.sent_at;
        test_case_eq_int(t, msg.read_age, 0, );
        test_case_eq_int(t, msg.edited_age, 0, );
        test_case_eq_int(t, msg.deleted_age, 0, );
        test_case_eq_int(t, msg.user_id_sender, 5, );
        test_case_eq_int(t, msg.user_id_recipient, 1, );
        test_case_eq_str(t, msg.content, MSG_CONTENT, );
        db_collect(memory_owner_db);
        break;
    }
    case 2: { // inbox
        if (!test_case_eq_int(t, response->type, action_type_inbox, )) return;
        test_case_eq_int(t, response->body.inbox.n_msgs, 1, );
        msg_t msg = response->body.inbox.msgs[0];
        test_case_eq_int(t, msg.id, gs_msg_id, );
        test_case_eq_long(t, msg.sent_at, gs_msg_sent_at, );
        test_case_eq_int(t, msg.read_age, 0, );
        test_case_eq_int(t, msg.edited_age, 0, );
        test_case_eq_int(t, msg.deleted_age, 0, );
        test_case_eq_int(t, msg.user_id_sender, 5, );
        test_case_eq_int(t, msg.user_id_recipient, 1, );
        test_case_eq_str(t, msg.content, MSG_CONTENT, );
        break;
    }
    case 3: // rm
        test_case_eq_int(t, response->type, action_type_rm, );
        break;
    default: test_fail(t, "wrong test->n_responses: %d", test->n_actions);
    }
}

TEST_SIGNATURE(NAME) {
    test_t test = {
        .t = test_start(STR(NAME)),
        .server = server,
        .db = db,
    };

    uuid4_t api_key_m1, api_key_p1;
    assert(uuid4_parse(&api_key_m1, API_KEY_MEMBER1));
    assert(uuid4_parse(&api_key_p1, API_KEY_PRO1));

    // Member logs in
    response_t response = action_evaluate(
        &(action_t) {
            .type = action_type_login,
            .with.login = {
                .api_key = api_key_m1,
                .password = SLICE_CONST("member1_mdp"),
            },
        },
        cfg, db, server);
    bool ok = test_case_eq_int(&test.t, response.type, action_type_login, "member logs in");
    gs_token_member1 = response.body.login.token;
    response_destroy(&response);
    if (!ok) return test.t;

    // Member sends message
    json_object *obj_input = input_file_fmt(IN_FILE(NAME, "_send"), gs_token_member1);
    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 1);
    ok = test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, "_send"), gs_msg_id);
    json_object_put(obj_input);
    json_object_put(obj_output);

    // if send output test failed, early return after logging out
    if (!ok) return test.t;

    // Pro logs in
    response = action_evaluate(
        &(action_t) {
            .type = action_type_login,
            .with.login = {
                .api_key = api_key_p1,
                .password = SLICE_CONST("pro1_mdp"),
            },
        },
        cfg, db, server);
    ok = test_case_eq_int(&test.t, response.type, action_type_login, "pro logs in");
    gs_token_pro1 = response.body.login.token;
    response_destroy(&response);
    if (!ok) return test.t;

    // Pro queries inbox
    obj_input = input_file_fmt(IN_FILE(NAME, "_inbox"), gs_token_pro1);
    obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 2);
    test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, "_send"), gs_msg_id, gs_msg_sent_at);

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

    // Member deletes message

    obj_input = input_file_fmt(IN_FILE(NAME, "_rm"), gs_token_member1, gs_msg_id);
    obj_output = tchattator413_interpret(obj_input, cfg, db, server, on_action, on_response, &test);
    test_case_n_actions(&test, 3);
    test_case_o_file_fmt(&test, obj_output, OUT_FILE(NAME, "_rm"));

    // Member logs out
    response = action_evaluate(
        &(action_t) {
            .type = action_type_logout,
            .with.logout.token = gs_token_member1 },
        cfg, db, server);
    ok &= test_case_eq_int(&test.t, response.type, action_type_logout, "member logs out");
    response_destroy(&response);

    return test.t;
}
