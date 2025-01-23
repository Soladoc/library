/// @file
/// @author Raphaël
/// @brief UUID unit tests - Main program
/// @date 23/01/2025

#include <strings.h>

#include "../src/util.h"
#include "../src/uuid.h"

#define STB_TEST_IMPLEMENTATION
#include "lib/stb_test.h"

int main() {
    static char const uuids[][UUID4_REPR_LENGTH] = {
        "f81d4fae-7dec-11d0-a765-00a0c91e6bf6",
        "F81D4FAE-7DEC-11D0-A765-00A0C91E6BF6",
        "F81d4faE-7deC-11D0-A765-00A0c91e6Bf6",
        "00000000-0000-0000-0000-000000000000",
        "ffffffff-ffff-ffff-ffff-ffffffffffff",
        "FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF",
    };

    static char const invalid_uuids[][UUID4_REPR_LENGTH] = {
        "f81g4fae-7dec-11d0-a765-00a0c91e6bf6",
        "f81d4fa-7dec-11d0-a765-00a0c91e6bf6",
        "f81d4fae-7dec11d0-a765-00a0c91e6bf6"
    };

    struct test t = test_start("uuid4");

    for (size_t i = 0; i < array_length(uuids); ++i) {
        uuid4_t uuid;
        test_case(&t, uuid4_from_repr(&uuid, uuids[i]), //
            "%.*s", UUID4_REPR_LENGTH, uuids[i]);
        char repr[UUID4_REPR_LENGTH];
        test_case(&t, strncasecmp(uuids[i], uuid4_repr(uuid, repr), UUID4_REPR_LENGTH) == 0, //
            "%.*s == %.*s", UUID4_REPR_LENGTH, uuids[i], UUID4_REPR_LENGTH, repr);
    }

    for (size_t i = 0; i < array_length(invalid_uuids); ++i) {
        uuid4_t uuid;
        test_case(&t, !uuid4_from_repr(&uuid, invalid_uuids[i]), //
            "%.*s", UUID4_REPR_LENGTH, invalid_uuids[i]);
    }

    test_conclude(&t, stdout);
}
