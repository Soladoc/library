/// @file
/// @author Raphaël
/// @brief General constants - Standalone header
/// @date 23/01/2025

#ifndef CONST_H
#define CONST_H

// https://en.wikipedia.org/wiki/X_macro

#define ADMIN_PASSWORD_HASH "$2y$10$YiDc/A/8DR9YSVohn7Dh9u5rb7DaiKvG/2iMRF3Xo8byNkOPEY0Sq"

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


#define HELP PROG " - A Tchattator413 implementation\n\
\n\
SYNOPSIS\n\
    " PROG " -[qv]... [-c FILE] [REQUEST]\n\
    " PROG " --dump-config\n\
    " PROG " --help\n\
    " PROG " --version\n\
\n\
DESCRIPTION\n\
    Reads JSON actions from standard input, or from the REQUEST argument if provided. Writes JSON results to standard output.\n\
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
    DB_HOST            DB host (required)\n\
    PGDB_PORT          DB port (required)\n\
    DB_NAME            DB name (required)\n\
    DB_USER            DB username (required)\n\
    DB_ROOT_PASSWORD   DB password (required)"

#define VERSION PROG " 1.0.0"

#endif // CONST_H
