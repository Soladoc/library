/// @file
/// @author Raphaël
/// @brief Tchattator413 JSON front-end - Main program
/// @date 23/01/2025

//#define DBG_INPUT_FILE "test/input/1.json"

#include "src/config.h"
#include "src/json-helpers.h"
#include "src/tchattator413.h"
#include "src/util.h"
#include <assert.h>
#include <getopt.h>
#include <json-c/json.h>
#include <stdio.h>
#include <stdlib.h>
#include <sysexits.h>
#include <unistd.h>

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
                    put_error("config already specified by previous argument\n");
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
    db_t *db = db_connect(verbosity);

    json_object *const input =
#ifdef DBG_INPUT_FILE
        json_object_from_file(DBG_INPUT_FILE)
#else
        json_object_from_fd(STDIN_FILENO)
#endif // DBG_INPUT_FILE
        ;

    if (!input) {
        put_error_json_c("failed to parse input\n");
        return EX_DATAERR;
    }

    server_t server = {};

    json_object *output = tchattator413_interpret(input, cfg, db, &server, NULL, NULL, NULL);

    // Results

    puts(json_object_to_json_string_ext(output, JSON_C_TO_STRING_PLAIN));

    // Deallocation

    json_object_put(input);
    json_object_put(output);

    db_destroy(db);
    config_destroy(cfg);
    server_destroy(&server);

    return EX_OK;
}
