/// @file
/// @author Raphaël
/// @brief General utilities - Standalone hedaer
/// @date 23/01/2025

#ifndef UTIL_H
#define UTIL_H

#include <stdarg.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define QUOTE(name) #name
#define STR(macro) QUOTE(macro)

#define CAT(x, y) CAT_(x, y)
#define CAT_(x, y) x##y

#define PROG "tct413"

#ifdef NDEBUG
#define put_log(fmt, ...) fprintf(stderr, PROG ": " fmt __VA_OPT__(, ) __VA_ARGS__)
#else
#define put_log(fmt, ...) fprintf(stderr, PROG ":" __FILE_NAME__ ":" STR(__LINE__) ": " fmt __VA_OPT__(, ) __VA_ARGS__)
#endif // NDEBUG

#define put_error(fmt, ...) put_log("error: " fmt __VA_OPT__(, ) __VA_ARGS__)

/// @brief Gets the necessary buffer size for a sprintf operation.
#define buffer_size(format, ...) (snprintf(NULL, 0, (format), __VA_ARGS__) + 1) // safe byte for \0

#define streq(x, y) (strcmp((x), (y)) == 0)
#define strneq(x, y, n) (strncmp((x), (y), (n)) == 0)

#define MAX(a, b) ((a) > (b) ? (a) : (b))
#define MIN(a, b) ((a) < (b) ? (a) : (b))

#define array_len(array) (sizeof(array) / sizeof((array)[0]))

#define COALESCE(a, b) ((a == NULL) ? (b) : (a))

#define errno_exit(of)      \
    do {                    \
        perror(of);         \
        exit(EXIT_FAILURE); \
    } while (0)

#ifdef __clang__
#define attr_flag_enum [[clang::flag_enum]]
#else
#define attr_flag_enum
#endif // __clang__

#ifdef __GNUC__
#define attr_format(archetype, string_index, first_to_check) __attribute__((format(archetype, string_index, first_to_check)))
#else
#define attr_format(archetype, string_index, first_to_check)
#endif // __GNUC__

char *strfmt(const char *fmt, ...) attr_format(printf, 1, 2);
char *vstrfmt(const char *fmt, va_list ap);

char *fslurp(FILE *fp);

#if __STDC_VERSION__ < 202000L
#define unreachable() abort();
#else
#include <stddef.h>
#endif

#endif // UTIL_H
