/// @file
/// @author Raphaël
/// @brief Tchattator413 testing - implementation of testing utilities
/// @date 1/02/2025

#include "tests_tchattator413.h"
#include "tchattator413/json-helpers.h"
#include <stdarg.h>

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

bool test_case_o(test_t *test, json_object *obj_output, char const *expected_output) {
    return test_case_wide(&test->t, streq(min_json(obj_output), expected_output), "output: %s == %s", min_json(obj_output), expected_output);
}

static inline char const *json_object_get_fmt(json_object *obj) {
    json_object *fmt_obj;
    return json_object_is_type(obj, json_type_object) && json_object_object_length(obj) == 1
        ? json_object_object_get_ex(obj, "$fmt", &fmt_obj)
            ? min_json(fmt_obj)
            : json_object_object_get_ex(obj, "$fmt_number", &fmt_obj)
            ? json_object_get_string(fmt_obj)
            : NULL
        : NULL;
}

static inline json_object *reduce_fmt_v(json_object *obj, va_list ap) {
    char const *fmt = json_object_get_fmt(obj);
    if (fmt) {
        // arguments -> json object
        char *fmted = vstrfmt(fmt, ap);
        json_object_put(obj);
        obj = json_tokener_parse(fmted);
        free(fmted);
        return obj;
    }

    switch (json_object_get_type(obj)) {
    case json_type_null: break;
    case json_type_boolean: break;
    case json_type_double: break;
    case json_type_int: break;
    case json_type_object: {
        struct lh_entry *entry;
        entry = json_object_get_object(obj)->head;
        while (entry) {
            entry->v = reduce_fmt_v(lh_entry_v(entry), ap);
            entry = entry->next;
        }
        break;
    }
    case json_type_array: {
        size_t len = json_object_array_length(obj);
        for (size_t i = 0; i < len; ++i) {
            json_object *old_obj = json_object_array_get_idx(obj, i);
            json_object *new_obj = reduce_fmt_v(old_obj, ap);
            if (new_obj != old_obj) json_object_array_put_idx(obj, i, new_obj);
        }
    }
    case json_type_string: break;
    }

    return obj;
}

json_object *input_file_fmt(const char *input_filename, ...) {
    FILE *finput = fopen(input_filename, "r");
    if (!finput) return NULL;
    char *input = fslurp(finput);
    fclose(finput);
    if (!input) return NULL;

    json_object *obj = json_tokener_parse(input);
    free(input);

    va_list ap;
    va_start(ap, input_filename);
    obj = reduce_fmt_v(obj, ap);
    va_end(ap);

    return obj;
}

static inline bool _json_object_eq_fmt_v(json_object *obj_actual, json_object *obj_expected, va_list ap) {
    // Special handling of our JSON pattern matching mechanism
    const char *fmt = json_object_get_fmt(obj_expected);
    if (fmt) {
        // json object -> arguments
        char const *str = json_object_get_string(obj_actual);
        int n = vsscanf(str, fmt, ap);
        return n != EOF;
    }

    json_type const actual_type = json_object_get_type(obj_actual), expected_type = json_object_get_type(obj_expected);
    if (actual_type != expected_type) return false;
    switch (actual_type) {
    case json_type_null: return true;
    case json_type_boolean: return json_object_get_boolean(obj_actual) == json_object_get_boolean(obj_expected);
#pragma GCC diagnostic push
#pragma GCC diagnostic ignored "-Wfloat-equal"
    case json_type_double: return json_object_get_double(obj_actual) == json_object_get_double(obj_expected);
#pragma GCC diagnostic pop
    case json_type_int: return json_object_get_int(obj_actual) == json_object_get_int(obj_expected);
    case json_type_object:
        // Two JSON objects are equal if they contain the same properties, regardless of order
        if (json_object_object_length(obj_actual) != json_object_object_length(obj_expected)) return false;
        json_object_object_foreach(obj_actual, k, a_v) {
            json_object *e_v;
            if (!json_object_object_get_ex(obj_expected, k, &e_v) || !_json_object_eq_fmt_v(a_v, e_v, ap)) return false;
        }
        return true;
    case json_type_array: {
        size_t const actual_len = json_object_array_length(obj_actual), expected_len = json_object_array_length(obj_expected);
        bool equal = actual_len == expected_len;
        for (size_t i = 0; equal && i < actual_len; ++i) {
            equal = _json_object_eq_fmt_v(
                json_object_array_get_idx(obj_actual, i),
                json_object_array_get_idx(obj_expected, i),
                ap);
        }
        return equal;
    }
    case json_type_string:
        return streq(json_object_get_string(obj_actual), json_object_get_string(obj_expected));
    default: unreachable();
    }
}

bool test_case_o_file_fmt(test_t *test, json_object *obj_output, char const *expected_output_filename, ...) {
    json_object *obj_output_expected = json_object_from_file(expected_output_filename);
    if (!obj_output_expected) {
        put_error_json_c("failed to read test output JSON file at '%s'\n", expected_output_filename);
        exit(EX_IOERR);
    }

    va_list ap;
    va_start(ap, expected_output_filename);
    bool ok = _json_object_eq_fmt_v(obj_output, obj_output_expected, ap);
    va_end(ap);
    json_object_put(obj_output_expected);
    return test_case_wide(&test->t, ok, "%s == cat %s", min_json(obj_output), expected_output_filename);
}

bool json_object_eq_fmt(json_object *obj_actual, json_object *obj_expected, ...) {
    va_list ap;
    va_start(ap, obj_expected);
    bool ok = _json_object_eq_fmt_v(obj_actual, obj_expected, ap);
    va_end(ap);
    return ok;
}
