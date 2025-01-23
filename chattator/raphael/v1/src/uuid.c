#include "uuid.h"

#include "assert.h"
#include <stdint.h>
#include <string.h>

/*
UUID     = 4hexOctet "-"
           2hexOctet "-"
           2hexOctet "-"
           2hexOctet "-"
           6hexOctet
hexOctet = HEXDIG HEXDIG
DIGIT    = %x30-39
HEXDIG   = DIGIT / "A" / "B" / "C" / "D" / "E" / "F"
*/

static inline char hex_half_to_repr(uint8_t value);
static inline uint8_t hex_repr_to_half(char c);

#define INVALID_HALF 255

#define X_42226 o o o o h o o h o o h o o h o o o o o o

char *uuid4_repr(uuid4_t uuid, char repr[const UUID4_REPR_LENGTH]) {
    size_t idata = 0, i = 0;
#define o                                                    \
    do {                                                     \
        repr[i++] = hex_half_to_repr(uuid.data[idata] >> 4); \
        repr[i++] = hex_half_to_repr(uuid.data[idata] & 15); \
        ++idata;                                             \
    } while (0);
#define h repr[i++] = '-';
    X_42226;
#undef o
#undef h
    return repr;
}

bool uuid4_from_repr(uuid4_t *uuid, char const repr[static const UUID4_REPR_LENGTH]) {
    size_t idata = 0, i = 0;
    uint8_t v1, v2;
#define o                                                            \
    do {                                                             \
        if ((v1 = hex_repr_to_half(repr[i++])) == INVALID_HALF       \
            || (v2 = hex_repr_to_half(repr[i++])) == INVALID_HALF) { \
            return false;                                            \
        }                                                            \
        uuid->data[idata++] = (v1 << 4) + v2;                        \
    } while (0);
#define h \
    if (repr[i++] != '-') return false;
    X_42226
#undef o
#undef h
    return true;
}

char hex_half_to_repr(uint8_t value) {
    assert(value < 16);
    return value < 10 ? '0' + value : 'a' - 10 + value;
}

uint8_t hex_repr_to_half(char c) {
    if ('0' <= c && c <= '9') {
        return c - '0';
    }
    if ('A' <= c && c <= 'F') {
        return c - 'A' + 10;
    }
    if ('a' <= c && c <= 'f') {
        return c - 'a' + 10;
    }
    return INVALID_HALF;
}
