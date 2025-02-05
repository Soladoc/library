/// @file
/// @author Raphaël
/// @brief Testing - Interface
/// @date 1/02/2025

#ifndef TEST_TCHATATOR413_H
#define TEST_TCHATATOR413_H

#include <assert.h>
#include <json-c.h>
#include <stb_test.h>

#include <tchatator413/cfg.h>
#include <tchatator413/db.h>
#include <tchatator413/json-helpers.h>
#include <tchatator413/server.h>

// Tests

// Tchatator413 test naming convention:
// test_tchatator413_<ROLE>_<ACTION>_<WITH...>
// ROLE is
//  - admin: administrator
//  - pro: professional
//  - member: membre
//  - invalid: invalid token / api key
// ACTION is an action name
// WITH are the action arguments, one or more, separated by '_'

#pragma GCC diagnostic ignored "-Wmissing-prototypes"
#pragma GCC diagnostic push
#pragma GCC diagnostic ignored "-Wcomment"
#define X_TESTS(X)                                               \
    X(empty)                                                     \
    X(malformed)                                                 \
    X(zero)                                                      \
                                                                 \
    X(admin_login_logout)                                        \
    X(admin_whois_1)                                             \
    X(admin_whois_imax)                                          \
    X(admin_whois_neg1)                                          \
    X(invalid_login)                                             \
    X(invalid_logout)                                            \
    X(invalid_whois_1)                                           \
    X(member1_login_login_logout_logout)                         \
    X(member1_login_logout_logout)                               \
    X(member1_login_logout)                                      \
    X(member1_login_member2_login_member1_logout_member2_logout) \
    X(member1_login_wrong_password)                              \
    X(member1_login)                                             \
    X(member1_send_pro1_inbox_member1_rm)                        \
    X(member1_whois_1_by_email)                                  \
    X(member1_whois_1_by_name)                                   \
    X(member1_whois_1)                                           \
    X(member1_whois_5_by_email)                                  \
    X(member1_whois_5_by_name)                                   \
    X(member1_whois_5)                                           \
    X(pro1_login_logout)                                         \
    X(pro1_inbox)                                                \
    //
#pragma GCC diagnostic pop

#define TEST_SIGNATURE(name) struct test CAT(test_tchatator413_, name)(cfg_t * cfg, db_t * db, server_t * server)

#define DECLARE_TEST(name) TEST_SIGNATURE(name);
X_TESTS(DECLARE_TEST)
#undef DECLARE_TEST

#define OUT_JSON(NAME, suffix) "test/tchatator413/json/" STR(NAME) "/out" suffix ".json"
#define OUT_JSONF(NAME, suffix) OUT_JSON(NAME, suffix) "f"
#define IN_JSON(NAME, suffix) "test/tchatator413/json/" STR(NAME) "/in" suffix ".json"
#define IN_JSONF(NAME, suffix) IN_JSON(NAME, suffix) "f"

// Implementation details

#include <stddef.h>

#define OUTPUT_500 "[{\"status\":500,\"body\":{}}]"

#define API_KEY_ADMIN "ed33c143-5752-4543-a821-00a187955a28"

// exist in the test DB
#define API_KEY_MEMBER1 "123e4567-e89b-12d3-a456-426614174000"
#define API_KEY_MEMBER2 "9ea59c5b-bb75-4cc9-8f80-77b4ce851a0b"
#define API_KEY_PRO1 "bb1b5a1f-a482-4858-8c6b-f4746481cffa"
#define API_KEY_PRO2 "52d43379-8f75-4fbd-8b06-d80a87b2c2b4"

// do not exist in the test db
#define API_KEY_NONE1 "00000000-0000-0000-0000-000000000000"
#define API_KEY_NONE2 "ffffffff-ffff-ffff-ffff-ffffffffffff"

#define API_KEY_MALFORMED "123e4567e89b12d3a456426614174000"

typedef struct {
    /// @brief Backing test.
    struct test t;
    int n_actions, n_responses;
    server_t *server;
    db_t *db;
} test_t;
_Static_assert(offsetof(test_t, t) == 0, "backing test must be at start of struct for implicit base type punning");

#define test_case_count(t, actual, expected, singular) test_case(t, actual == expected, "expected %d %s%s, got %d", expected, singular, expected == 1 ? "" : "s", actual)

bool uuid4_eq_repr(uuid4_t uuid, char const repr[static const UUID4_REPR_LENGTH]);

json_object *load_json(char const *input_filename);
json_object *load_jsonf(char const *input_filename, ...);

#define test_output_json(t, obj_output, obj_expected_output) \
    test_case_wide(t, json_object_equal(obj_output, obj_expected_output), "output: %s == %s", min_json(obj_output), min_json(obj_expected_output))

/// @brief output from JSON file test case
bool test_output_json_file(test_t *test, json_object *obj_output, char const *expected_output_filename);

/// @brief Base on_action event handler.
test_t *base_on_action(void *test);
/// @brief Base on_response event handler.
test_t *base_on_response(void *test);

/// @brief Actions count test case
void test_case_n_actions(test_t *test, int expected);

/// @brief Test two JSON objects are equal for equality, with pattern matching using formatting.
///
/// Retrieves the values contained in the variadic arguments.
///
/// @p obj_expected may have contain formatting syntax to indicate the format of the expected values instead of hard strings.
bool json_object_eq_fmt(json_object *obj_actual, json_object *obj_expected);

#define test_case_eq_int(t, actual, expected, fmt) test_case((t), actual == expected, fmt " == %d", actual)
#define test_case_eq_int64(t, actual, expected, fmt) test_case((t), actual == expected, fmt " == %ld", actual)
#define test_case_eq_str(t, actual, expected, fmt) test_case((t), streq(actual, expected), fmt " == %s", actual)
#define test_case_eq_json_object(t, actual, expected, fmt) test_case((t), json_object_equal(actual, expected), fmt " == %s", min_json(actual))
#define test_case_eq_uuid(t, actual, expected, fmt)                                                    \
    do {                                                                                               \
        char repr[UUID4_REPR_LENGTH];                                                                  \
        uuid4_repr(actual, repr);                                                                      \
        test_case((t), uuid4_eq_repr(actual, expected), fmt " == %" STR(UUID4_REPR_LENGTH) "s", repr); \
    } while (0)

#endif // TEST_TCHATATOR413_H
