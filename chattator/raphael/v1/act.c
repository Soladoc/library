/// @file
/// @author Raphaël
/// @brief Tchattator413 JSON front-end - Main program
/// @date 23/01/2025

#include <assert.h>
#include <getopt.h>
#include <json-c/json.h>
#include <stdio.h>
#include <stdlib.h>
#include <sysexits.h>
#include <unistd.h>

#include "src/action.h"
#include "src/util.h"

#define PROG "act"

#define HELP PROG " - A Tchattator413 implementation\n\
\n\
Usage: " PROG " -[qv]... [--help] [--version]\n\
\n\
Reads JSON actions from standard input. Writes JSON results to standard output.\n\
\n\
-q                More quiet (can be repeated)\n\
-v                More verbose (can be repeated)\n\
--help            Show this help\n\
--version         Show version\n\
\n\
ENVIRONMENT\n\
\n\
DB_HOST           DB host\n\
PGDB_PORT         DB port\n\
DB_NAME           DB name\n\
DB_USER           DB username\n\
DB_ROOT_PASSWORD  DB password"

#define VERSION PROG " 1.0.0"

static inline json_object *act(json_object *const, db_t *);

enum { EX_NODB = EX__MAX + 1 };

int main(int argc, char **argv) {
    int verbosity = 0;
    // Arguments
    {
        enum {
            opt_help,
            opt_version,
        };
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
                puts(HELP);
                return EX_OK;
            case opt_version:
                puts(VERSION);
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

    json_object *const output = json_object_new_array();

    // Usage

    json_type const input_type = json_object_get_type(input);
    json_object *item;
    switch (input_type) {
    case json_type_array: {
        size_t const len = json_object_array_length(input);
        for (size_t i = 0; i < len; ++i) {
            json_object *const action = json_object_array_get_idx(input, i);
            assert(action);

            if ((item = act(action, db))) json_object_array_add(output, item);
        }
        break;
    }
    case json_type_object:
        if ((item = act(input, db))) json_object_array_add(output, item);
        break;
    default:
        handle_error("error: invalid request (expected array or object, got %s)", json_type_to_name(input_type));
    }

    // Results

    fputs(json_object_to_json_string(output), stdout);

    // Deallocation

#ifndef NDEBUG // The OS will release the memory anyway
    json_object_put(input);
    json_object_put(output);

    db_destroy(db);
#endif // NBDEBUG

    return EX_OK;
}

json_object *act(json_object *const action_obj, db_t *db) {
    struct action action;
    struct response response;
    if (!action_parse(&action, action_obj, db)) return NULL;

    if (!action_evaluate(&response, &action, db)) return NULL;

    json_object *json_response = response_to_json(&response);

    action_destroy(&action);

    return json_response;
}
