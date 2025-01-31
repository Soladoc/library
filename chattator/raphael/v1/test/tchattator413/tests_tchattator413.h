#ifndef TEST_TCHATTATOR413_H
#define TEST_TCHATTATOR413_H

#include <stb_test.h>

#include <tchattator413/cfg.h>
#include <tchattator413/db.h>
#include <tchattator413/server.h>

// Tests

// Tchattator413 test naming convention:
// test_tchattator413_<ROLE>_<ACTION>_<WITH...>
// ROLE is
//  - admin: administrator
//  - pro: professional
//  - member: membre
//  - invalid: invalid token / api key
// ACTION is an action name
// WITH are the action arguments, one or more, separated by '_'

/// @brief Tchattator413 test: empty input (0 actions)
struct test test_tchattator413_zero(cfg_t *cfg, db_t *db, server_t *server);

struct test test_tchattator413_admin_whois_1(cfg_t *cfg, db_t *db, server_t *server);
struct test test_tchattator413_admin_whois_neg1(cfg_t *cfg, db_t *db, server_t *server);

struct test test_tchattator413_invalid_whois_1(cfg_t *cfg, db_t *db, server_t *server);
struct test test_tchattator413_invalid_login(cfg_t *cfg, db_t *db, server_t *server);


// Implementation details

#include <stddef.h>

#define OUTPUT_500 "[{\"status\":500,\"has_next_page\":false,\"body\":{}}]"

#define ADMIN_API_KEY_REPR "ed33c143-5752-4543-a821-00a187955a28"
#define ADMIN_API_KEY uuid4_of(0xed, 0x33, 0xc1, 0x43, 0x57, 0x52, 0x45, 0x43, 0xa8, 0x21, 0x00, 0xa1, 0x87, 0x95, 0x5a, 0x28)
#define INVALID_API_KEY_REPR "00123400-0000-0000-0100-000000000000"
#define INVALID_API_KEY uuid4_of(0x00, 0x12, 0x34, 0x00, 0x00, 0x00, 0x00, 0x00, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00)

typedef struct {
    /// @brief Backing test.
    struct test t;
    int n_actions, n_responses;
} test_t;

_Static_assert(offsetof(test_t, t) == 0, "backing test must be at start of struct for implicit base type punning");

#define new_test() { .t = test_start(__func__) }

#define begin_on_action(ptest) \
    ++((test_t *)ptest)->n_actions

#define begin_on_response(ptest) \
    ++((test_t *)ptest)->n_responses

#define min_json(obj) json_object_to_json_string_ext(obj, JSON_C_TO_STRING_PLAIN)

#define test_case_i(test, obj_input, i) test_case_n(&test.t, obj_input, "input: " i) // input JSON test case
#define test_case_o(test, obj_output, o) test_case_n(&test.t,                    \
    streq(min_json(obj_output), o), "output: %s == " o, min_json(obj_output)) // output JSON test case

#define test_case_count(t, actual, expected, singular) test_case(t, actual == expected, "expected %d %s%s, got %d", expected, singular, expected == 1 ? "" : "s", actual)
#define test_case_n_actions(test, expected)                               \
    do {                                                                  \
        test_case_count(&test.t, test.n_actions, expected, "action");     \
        test_case_count(&test.t, test.n_responses, expected, "response"); \
    } while (0)


#endif // TEST_TCHATTATOR413_H