/// @file
/// @author Raphaël
/// @brief General constants - Standalone header
/// @date 23/01/2025

#ifndef CONST_H
#define CONST_H

#include <sysexits.h>

/// @brief Additional sysexits codes.
enum {
    /// @brief Exit code for when the database connection failed.
    EX_NODB = EX__MAX + 1,
};

/// @brief The bcrypt hash of the administrator password.
#define ADMIN_PASSWORD_HASH "$2y$10$uggPw5mEgOJyxNVqkXF4uuGzFtT2xmgHKMstdMxsYObPjOlR1143O"

/// @brief X-macro that expands to the list of actions
#define X_ACTIONS(X) \
    X(login)         \
    X(logout)        \
    X(whois)         \
    X(send)          \
    X(motd)          \
    X(inbox)         \
    X(outbox)        \
    X(edit)          \
    X(rm)            \
    X(block)         \
    X(unblock)       \
    X(ban)           \
    X(unban)

/// @brief Fallback database host if the corresponding environment variable isn't defined.
#define FALLBACK_DB_HOST "413.ventsdouest.dev"
/// @brief Fallback database post if the corresponding environment variable isn't defined.
#define FALLBACK_PGDB_PORT "5432"
/// @brief Fallback database name if the corresponding environment variable isn't defined.
#define FALLBACK_DB_NAME "sae413_test" // Run on the test DB by default
/// @brief Fallback database username if the corresponding environment variable isn't defined.
#define FALLBACK_DB_USER "sae"
/// @brief Fallback database password if the corresponding environment variable isn't defined.
#define FALLBACK_DB_ROOT_PASSWORD "bib3loTs-CRues-rdv"

/// @brief The name of the program.
#define PROG "tct413"

/// @brief The program's helpstring.
#define HELP PROG " - A Tchatator413 implementation\n\
\n\
SYNOPSIS\n\
    " PROG " -[qv]... [-c FILE]\n\
    " PROG " -[qv]... [-c FILE] -i [REQUEST]\n\
    " PROG " --dump-config\n\
    " PROG " --help\n\
    " PROG " --version\n\
\n\
DESCRIPTION\n\
    A Tchatator413-compliant server based on Unix sockets. Accepts JSON, responds with JSON.\n\
\n\
    Mandatory arguments to long options are mandatory for short options too.\n\
\n\
    -q, --quiet        More quiet (can be repeated)\n\
    -v, --verbose      More verbose (can be repeated)\n\
    -i, --interactive  Run in interactive mode (read from STDIN or argument)\n\
    -c, --config=FILE  Configuration file\n\
    --dump-config      Dump current configuration\n\
    --help             Show this help\n\
    --version          Show version\n\
\n\
ENVIRONMENT\n\
    DB_HOST            DB host      Fallback to: " FALLBACK_DB_HOST "\n\
    PGDB_PORT          DB port      Fallback to: " FALLBACK_PGDB_PORT "\n\
    DB_NAME            DB name      Fallback to: " FALLBACK_DB_NAME "\n\
    DB_USER            DB username  Fallback to: " FALLBACK_DB_USER "\n\
    DB_ROOT_PASSWORD   DB password  Fallback to: (it's a secret)"

/// @brief The program's versionstring.
#define VERSION PROG " 1.0.1"

#endif // CONST_H
