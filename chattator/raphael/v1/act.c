#include <assert.h>
#include <getopt.h>
#include <json-c/json.h>
#include <stdio.h>
#include <stdlib.h>
#include <sysexits.h>
#include <unistd.h>

#include "src/action.h"
#include "src/util.h"

bool act(json_object *const, db_t *);

enum { EX_NODB = EX__MAX + 1 };

int main(int argc, char **argv) {
    int verbosity = 0;
    // Arguments
    {
        enum { opt_help = 1,
               opt_version };
        struct option long_options[] = {
            {
                .name = "help",
                .val = opt_help,
            },
            {
                .name = "version",
                .val = opt_version,
            },
            {},
        };

        int opt;
        while (-1 != (opt = getopt_long(argc, argv, "qvh:", long_options, NULL))) {
            switch (opt) {
            case opt_help:
                puts("help string");
                return EX_OK;
            case opt_version:
                puts("version string");
                return EX_OK;
            case 'q': --verbosity; break;
            case 'v': ++verbosity; break;
            case '?':
                return EX_USAGE;
            }
        }
    }

    // Allocation
    json_object *const input = json_object_from_fd(STDIN_FILENO);
    if (!input) return EX_DATAERR;

    db_t *db = db_connect(verbosity);
    if (!db) return EX_NODB;

    json_type const input_type = json_object_get_type(input);
    switch (input_type) {
    case json_type_array: {
        size_t const len = json_object_array_length(input);
        for (size_t i = 0; i < len; ++i) {
            json_object *const action = json_object_array_get_idx(input, i);
            assert(action);
            act(action, db);
        }
        break;
    }
    case json_type_object:
        act(input, db);
        break;
    default:
        handle_error("error: invalid request (expected array or object, got %s)", json_type_to_name(input_type));
    }

    // Usage

    // Deallocation

    json_object_put(input);

    return EX_OK;
}

bool act(json_object *const action_obj, db_t *db) {
    struct action action;
    if (!action_parse(&action, action_obj, db)) return false;

    action_explain(&action, stdout);

    action_destroy(&action);

    return true;
}
