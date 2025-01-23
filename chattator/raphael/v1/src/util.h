#ifndef UTIL_H
#define UTIL_H

#include <stdio.h>
#include <string.h>

#define QUOTE(name) #name
#define STR(macro) QUOTE(macro)
#define STRLEN(strlit) (sizeof (strlit) - 1)

#define handle_error(...)             \
    do {                              \
        fprintf(stderr, __VA_ARGS__); \
        exit(EXIT_FAILURE);           \
    } while (0)

/// @brief Gets the necessary buffer size for a sprintf operation.
#define buffer_size(format, ...) (snprintf(NULL, 0, (format), __VA_ARGS__) + 1) // safe byte for \0

#define handle_json_error() handle_error("error: json-c: %s\n", json_util_get_last_err())

#define streq(x, y) (strcmp((x), (y)) == 0)

#define max(a,b) ((a) > (b) ? (a) : (b))
#define min(a,b) ((a) < (b) ? (a) : (b))

#define array_length(array) (sizeof(array) / sizeof((array)[0]))

#endif // UTIL_H