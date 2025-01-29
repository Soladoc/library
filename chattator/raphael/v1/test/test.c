#include <stdlib.h>
#include "tests.h"

int main() {
    bool success = test_uuid();

    observe_put_role();

    return success ? EXIT_SUCCESS : EXIT_FAILURE;
}