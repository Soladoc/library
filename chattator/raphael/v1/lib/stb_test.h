/** @file
 * @author 5cover (Scover)
 * @brief Quick unit testing library
 * @copyright Public Domain - The Unlicense
 * @date 30/01/2024
 * @details
 * See README.md for details and usage.
 */

#ifndef STB_TEST_H_
#define STB_TEST_H_

#ifndef STB_TEST_DEFINITION
/// @brief Defines modifier keywords for the definitions. Example value: static inline
#define STB_TEST_DEFINITION
#endif

#include <stdbool.h>
#include <stdio.h>

#ifdef __GNUC__
#define _stbtest_attr_format(archetype, string_index, first_to_check) __attribute__((format(archetype, string_index, first_to_check)))
#else
#define _stbtest_attr_format(archetype, string_index, first_to_check)
#endif // __GNUC__

struct test {
    char const *name;
    struct _stbtest_case *cases;
};

struct _stbtest_case {
    bool ok;
    unsigned line;
    char const *expr;
    char *name;
};

STB_TEST_DEFINITION struct test test_start(char const *name);

#define test_case(test, expr, name, ...) _test_case(__LINE__, (test), (expr), #expr, (name)__VA_OPT__(, ) __VA_ARGS__)

STB_TEST_DEFINITION bool _test_case(unsigned line, struct test *test, bool ok, char const *expr, char const *fmt_name, ...)
    _stbtest_attr_format(printf, 5, 6);

STB_TEST_DEFINITION bool test_end(struct test *test, FILE *output);

#endif // STB_TEST_H_

#ifdef STB_TEST_IMPLEMENTATION

#include <stb_ds.h>

#include <math.h>
#include <stdarg.h>

#define _stbtest_digit_count(n, base) ((n) == 0 ? 1 : (int)(log(n) / log(base)) + 1)

struct test test_start(char const *name) {
    return (struct test) {
        .name = name,
        .cases = NULL,
    };
}

bool _test_case(unsigned line, struct test *test, bool ok, char const *expr, char const *fmt_name, ...) {
    va_list ap;

    va_start(ap, fmt_name);
    size_t name_size = vsnprintf(NULL, 0, fmt_name, ap) + 1;
    va_end(ap);

    char *name = malloc(sizeof *name * name_size);
    if (!name) {
        abort();
    }

    va_start(ap, fmt_name);
    vsnprintf(name, name_size, fmt_name, ap);
    va_end(ap);

    arrput(test->cases,
        ((struct _stbtest_case) {
            .ok = ok,
            .line = line,
            .expr = expr,
            .name = name,
        }));

    return ok;
}

bool test_end(struct test *test, FILE *output) {
    // Establish case success counts and column lengths
    int i, nb_ko = 0, nb_ok = 0;
    
    int col_len_expr = sizeof "expr";
    int col_len_name = sizeof "name";

    for (i = 0; i < arrlenu(test->cases); ++i) {
        struct _stbtest_case const *c = &test->cases[i];
        c->ok ? ++nb_ok : ++nb_ko;

        size_t len;
        if ((len = strlen(c->expr)) > col_len_expr) col_len_expr = len;
        if ((len = strlen(c->name)) > col_len_name) col_len_name = len;
    }

    // Show table if test failed
    if (nb_ko != 0) {
        int const col_len_num = _stbtest_digit_count(arrlenu(test->cases), 10);
        int const col_len_line = _stbtest_digit_count(arrlast(test->cases).line, 10);

        for (i = 0; i < col_len_num; ++i)
            putc('#', output);
        fprintf(output, " | OK | %*s | %-*s | %-*s |\n",
            col_len_line, "L",
            col_len_expr, "expr",
            col_len_name, "name");

        for (i = 0; i < col_len_num; ++i)
            putc('-', output);
        fputs(" | -- | ", output);
        for (i = 0; i < col_len_line; ++i)
            putc('-', output);
        fputs(" | ", output);
        for (i = 0; i < col_len_expr; ++i)
            putc('-', output);
        fputs(" | ", output);
        for (i = 0; i < col_len_name; ++i)
            putc('-', output);
        fputs(" |\n", output);

        // Print cases

        for (i = 0; i < arrlenu(test->cases); ++i) {
            struct _stbtest_case const *const c = &test->cases[i];
            fprintf(output, "%*d | %s | %*u | %-*s | %-*s |\n",
                col_len_num, i,
                c->ok ? "\033[32;49mOK\033[39;49m" : "\033[31;49mKO\033[39;49m",
                col_len_line, c->line,
                col_len_expr, c->expr,
                col_len_name, c->name);
        }
    }

    // Print summary
    fprintf(output, "test %s: %d ko, %d ok, %d total: %s\n",
            nb_ko == 0 ? "\033[32;49msuccess\033[39;49m" : "\033[31;49mfailure\033[39;49m",
            nb_ko,
            nb_ok,
            nb_ko + nb_ok,
            test->name);

    // Deallocate
    for (i = 0; i < arrlenu(test->cases); ++i) free(test->cases[i].name); // Free test
    arrfree(test->cases);

    return nb_ko == 0;
}

#endif // STB_TEST_IMPLEMENTATION
