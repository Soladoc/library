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
#include "src/config.h"
#include "src/json-helpers.h"
#include "src/util.h"

#define HELP PROG " - A Tchattator413 implementation\n\
\n\
SYNOPSIS\n\
    " PROG " -[qv]... [-c FILE]\n\
    " PROG " --dump-config\n\
    " PROG " --help\n\
    " PROG " --version\n\
\n\
DESCRIPTION\n\
    Reads JSON actions from standard input. Writes JSON results to standard output.\n\
\n\
    Mandatory arguments to long options are mandatory for short options too.\n\
\n\
    -q, --quiet        More quiet (can be repeated)\n\
    -v, --verbose      More verbose (can be repeated)\n\
    -c, --config=FILE  Configuration file\n\
    --dump-config      Dump current configuration\n\
    --help             Show this help\n\
    --version          Show version\n\
\n\
ENVIRONMENT\n\
    DB_HOST            DB host\n\
    PGDB_PORT          DB port\n\
    DB_NAME            DB name\n\
    DB_USER            DB username\n\
    DB_ROOT_PASSWORD   DB password"

#define VERSION PROG " 1.0.0"

static inline json_object *act(cfg_t *cfg, db_t *db, json_object *const obj_action);

enum { EX_NODB = EX__MAX + 1 };

int main(int argc, char **argv) {
    int verbosity = 0;
    bool dump_config = false;

    cfg_t *cfg = NULL;

    // Arguments
    {
        enum {
            opt_help,
            opt_version,
            opt_dump_config,
            opt_quiet = 'q',
            opt_verbose = 'v',
            opt_config = 'c',
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
            {
                .name = "dump-config",
                .val = opt_dump_config,
            },
            {
                .name = "quiet",
                .val = opt_quiet,
            },
            {
                .name = "verbose",
                .val = opt_verbose,
            },
            {
                .name = "config",
                .val = opt_config,
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
            case opt_dump_config:
                dump_config = true;
                break;
            case opt_quiet: --verbosity; break;
            case opt_verbose: ++verbosity; break;
            case opt_config:
                if (cfg) {
                    put_error("config already specified by previous argument");
                    return EX_USAGE;
                }
                cfg = config_from_file(optarg);
                if (!cfg) return EX_CONFIG;
                break;
            case '?':
                puts(HELP);
                return EX_USAGE;
            }
        }
    }

    if (!cfg) cfg = config_defaults();

    if (dump_config) {
        config_dump(cfg);
        config_destroy(cfg);
        return EXIT_SUCCESS;
    }

    // Allocation
    json_object *const input = json_object_from_fd(STDIN_FILENO);
    if (!input) {
        put_error_json_c("failed to parse input\n");
        return EX_DATAERR;
    }

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

            if ((item = act(cfg, db, action))) json_object_array_add(output, item);
        }
        break;
    }
    case json_type_object:
        if ((item = act(cfg, db, input))) json_object_array_add(output, item);
        break;
    default:
        put_error("invalid request (expected array or object, got %s)\n", json_type_to_name(input_type));
    }

    // Results

    fputs(json_object_to_json_string(output), stdout);

    // Deallocation

#ifndef NDEBUG // The OS will release the memory anyway
    json_object_put(input);
    json_object_put(output);

    db_destroy(db);
    config_destroy(cfg);
#endif // NBDEBUG

    return EX_OK;
}

json_object *act(cfg_t *cfg, db_t *db, json_object *const obj_action) {
    errstatus_t err;

    action_t action;
    if (!action_parse(&action, obj_action, cfg, db)) return NULL;

    response_t response;
    if (!action_evaluate(&action, &response, cfg, db)) return NULL; 

    json_object *json_response = response_to_json(&response);

    action_destroy(&action);

    return json_response;
}
