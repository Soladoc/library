#ifndef UTIL_H
#define UTIL_H

#include <stdio.h>
#include <string.h>

#define handle_error(...)             \
    do {                              \
        fprintf(stderr, __VA_ARGS__); \
        exit(EXIT_FAILURE);           \
    } while (0)

#define handle_json_error() handle_error("json-c: error: %s\n", json_util_get_last_err())

#define streq(x, y) (strcmp((x), (y)) == 0)

#define max(a,b) ((a) > (b) ? (a) : (b))
#define min(a,b) ((a) < (b) ? (a) : (b))

#endif // UTIL_H