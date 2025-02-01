/// @file
/// @author Raphaël
/// @brief General utilities - Standalone hedaer
/// @date 23/01/2025

#ifndef UTIL_H
#define UTIL_H

#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define QUOTE(name) #name
#define STR(macro) QUOTE(macro)

#define CAT(x, y) CAT_(x, y)
#define CAT_(x, y) x##y

#define PROG "act"

#define put_error(fmt, ...) fprintf(stderr, PROG ": error: " fmt __VA_OPT__(, ) __VA_ARGS__)

/// @brief Gets the necessary buffer size for a sprintf operation.
#define buffer_size(format, ...) (snprintf(NULL, 0, (format), __VA_ARGS__) + 1) // safe byte for \0

#define streq(x, y) (strcmp((x), (y)) == 0)
#define strneq(x, y, n) (strncmp((x), (y), (n)) == 0)

#define MAX(a, b) ((a) > (b) ? (a) : (b))
#define MIN(a, b) ((a) < (b) ? (a) : (b))

#define array_length(array) (sizeof(array) / sizeof((array)[0]))

#define COALESCE(a, b) ((a == NULL) ? (b) : (a))

#define errno_exit(of)    \
    do {                    \
        perror(of);         \
        exit(EXIT_FAILURE); \
    } while (0)

#ifdef __clang__
#define attr_flag_enum [[clang::flag_enum]]
#else
#define attr_flag_enum
#endif // __clang__

#endif // UTIL_H
