#include "tests.h"
#include <stdlib.h>

#define VERBOSITY TEST_SUMMARY

int main() {
    bool const success = test_uuid(VERBOSITY);

    if (VERBOSITY == TEST_VERBOSE) {
        observe_put_role();
    }

    return success ? EXIT_SUCCESS : EXIT_FAILURE;
}
