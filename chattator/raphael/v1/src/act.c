#include <assert.h>
#include <json-c/json.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

#include "const.h"
#include "util.h"

void act(json_object *const);

int main() {
    // Allocation
    json_object *const input = json_object_from_fd(STDIN_FILENO);
    if (!input) handle_json_error();

    json_type const input_type = json_object_get_type(input);
    switch (input_type) {
    case json_type_array: {
        size_t const len = json_object_array_length(input);
        for (size_t i = 0; i < len; ++i) {
            json_object *const action = json_object_array_get_idx(input, i);
            assert(action);
            act(action);
        }
        break;
    }
    case json_type_object:
        act(input);
        break;
    default:
        handle_error("error: invalid request (expected array or object, got %s)", json_type_to_name(input_type));
    }

    // Usage

    // Deallocation

    json_object_put(input);

    return EXIT_SUCCESS;
}

void act(json_object *const action) {
    printf("%s\n", json_object_to_json_string(action));
}
