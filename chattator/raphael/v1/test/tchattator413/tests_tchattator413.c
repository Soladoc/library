/// @file
/// @author Raphaël
/// @brief Tchattator413 testing - implementation of testing utilities
/// @date 1/02/2025

#include "tests_tchattator413.h"

bool uuid4_eq_repr(uuid4_t uuid, char const repr[static const UUID4_REPR_LENGTH]) {
    uuid4_t parsed_repr;
    assert(uuid4_parse(&parsed_repr, repr));
    return uuid4_eq(uuid, parsed_repr);
}

test_t *base_on_action(void *test) {
    test_t *t = (test_t *)test;
    ++t->n_actions;
    return t;
}

test_t *base_on_response(void *test) {
    test_t *t = (test_t *)test;
    ++t->n_responses;
    return t;
}

void test_case_n_actions(test_t *test, int expected) {
    test_case_count(&test->t, test->n_actions, expected, "action");
    test_case_count(&test->t, test->n_responses, expected, "response");
}
