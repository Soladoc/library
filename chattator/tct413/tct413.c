/// @file
/// @author Raphaël
/// @brief Tchatator413 server - Main program
/// @date 23/01/2025

#include <assert.h>
#include <getopt.h>
#include <json-c.h>
#include <stdio.h>
#include <stdlib.h>
#include <tchatator413/cfg.h>
#include <tchatator413/json-helpers.h>
#include <tchatator413/tchatator413.h>
#include <tchatator413/util.h>
#include <unistd.h>

/* to run it
set -a
. .env
set +a
./tct413

testing:
nc 127.0.0.1 4113 <<< '[]'

*/

static inline char const *getenv_or(char const *name, char const *fallback) {
    char *value = getenv(name);
    return value ? value : fallback;
}

int main(int argc, char **argv) {
    int verbosity = 0;
    bool dump_config = false, interactive = false;

    cfg_t *cfg = NULL;

    // Arguments
    {
        enum {
            opt_help,
            opt_version,
            opt_dump_config,
            opt_quiet = 'q',
            opt_verbose = 'v',
            opt_interactive = 'i',
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
                .name = "interactive",
                .val = opt_interactive,
            },
            {
                .name = "config",
                .val = opt_config,
            },
            { 0 },
        };

        int opt;
        while (-1 != (opt = getopt_long(argc, argv, "qvic:", long_options, NULL))) {
            switch (opt) {
            case opt_help:
                puts(HELP);
                return EXIT_SUCCESS;
            case opt_version:
                puts(VERSION);
                return EXIT_SUCCESS;
            case opt_dump_config:
                dump_config = true;
                break;
            case opt_quiet: --verbosity; break;
            case opt_verbose: ++verbosity; break;
            case opt_interactive: interactive = true; break;
            case opt_config:
                if (cfg) {
                    cfg_log(cfg, log_error, "config already specified by previous argument\n");
                    return EX_USAGE;
                }
                cfg = cfg_from_file(optarg);
                if (!cfg) return EX_CONFIG;
                break;
            case '?':
                puts(HELP);
                return EX_USAGE;
            }
        }
    }

    if (!cfg) cfg = cfg_defaults();

    int result;

    if (dump_config) {
        cfg_dump(cfg);
        result = EX_OK;
    } else {
        db_t *db = db_connect(cfg, verbosity,
            getenv_or("DB_HOST", FALLBACK_DB_HOST),
            getenv_or("PGDB_PORT", FALLBACK_PGDB_PORT),
            getenv_or("DB_NAME", FALLBACK_DB_NAME),
            getenv_or("DB_USER", FALLBACK_DB_USER),
            getenv_or("DB_ROOT_PASSWORD", FALLBACK_DB_ROOT_PASSWORD));
        if (!db) return EX_NODB;

        server_t *server = server_create(server_rate_limiting);

        result = interactive
            ? tchatator413_run_interactive(cfg, db, server, argc, argv)
            : tchatator413_run_socket(cfg, db, server);

        server_destroy(server);
        db_destroy(db);
    }

    cfg_destroy(cfg);

    return result;
}
