/// @file
/// @author Raphaël
/// @brief General constants - Standalone header
/// @date 23/01/2025

#ifndef CONST_H
#define CONST_H

#include <sysexits.h>

// Additional sysexits
enum { EX_NODB = EX__MAX + 1 };

// https://en.wikipedia.org/wiki/X_macro

#define ADMIN_PASSWORD_HASH "$2y$10$uggPw5mEgOJyxNVqkXF4uuGzFtT2xmgHKMstdMxsYObPjOlR1143O"

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

#define FALLBACK_DB_HOST "413.ventsdouest.dev"
#define FALLBACK_PGDB_PORT "5432"
#define FALLBACK_DB_NAME "sae413_test"
#define FALLBACK_DB_USER "sae"
#define FALLBACK_DB_ROOT_PASSWORD "bib3loTs-CRues-rdv"

#define HELP PROG " - A Tchattator413 implementation\n\
\n\
SYNOPSIS\n\
    " PROG " -[qv]... [-c FILE]\n\
    " PROG " -[qv]... [-c FILE] -i [REQUEST]\n\
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

#define VERSION PROG " 1.0.0"

#endif // CONST_H
