/// @file
/// @author Raphaël
/// @brief Tchattator413 JSON front-end - Main program
/// @date 23/01/2025

#include <assert.h>
#include <getopt.h>
#include <json-c/json.h>
#include <stdio.h>
#include <stdlib.h>
#include <tchattator413/cfg.h>
#include <tchattator413/json-helpers.h>
#include <tchattator413/tchattator413.h>
#include <tchattator413/util.h>
#include <unistd.h>

/* to run it
set -a
. .env
set +a
./tct413

testing:
nc 127.0.0.1 4113 <<< '[]'

*/

static inline char *require_env(char const *name) {
    char *value = getenv(name);
    if (value) return value;
    put_error("environment variable '%s' is required\n", name);
    exit(EX_USAGE);
}

__asm__(".symver realpath,realpath@GLIBC_2.31");
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
            {0},
        };

        int opt;
        while (-1 != (opt = getopt_long(argc, argv, "qvh:", long_options, NULL))) {
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
                    put_error("config already specified by previous argument\n");
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
        db_t *db = db_connect(verbosity,
            require_env("DB_HOST"),
            require_env("PGDB_PORT"),
            require_env("DB_NAME"),
            require_env("DB_USER"),
            require_env("DB_ROOT_PASSWORD"));
        if (!db) return EX_NODB;

        server_t *server = server_create(server_rate_limiting);

        result = interactive
            ? tchattator413_run_console(cfg, db, server, argc, argv)
            : tchattator413_run_server(cfg, db, server);

        server_destroy(server);
        db_destroy(db);
    }

    cfg_destroy(cfg);

    return result;
}
