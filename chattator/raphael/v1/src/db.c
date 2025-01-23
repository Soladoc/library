/// @file
/// @author Raphaël
/// @brief DAL - Implementation
/// @date 23/01/2025

#include <netinet/in.h>
#include <postgresql/libpq-fe.h>

#include <stdlib.h>

#include "db.h"
#include "util.h"

#define SCHEMA_PACT "pact"

#define conn_param(param) coalesce(getenv(#param), STR(param))

db_t *db_connect(int verbosity) {
    PGconn *db = PQsetdbLogin(
        conn_param(DB_HOST),
        conn_param(PGDB_PORT),
        NULL, NULL,
        conn_param(DB_NAME),
        conn_param(DB_USER),
        conn_param(DB_ROOT_PASSWORD));

    if (PQstatus(db) != CONNECTION_OK) {
        fprintf(stderr, "error: PQsetdbLogin: %s\n", PQerrorMessage(db));
        PQfinish(db);
        return NULL;
    }

    PGVerbosity v;
    if (verbosity <= -2)
        v = PQERRORS_SQLSTATE;
    else if (verbosity == -1)
        v = PQERRORS_TERSE;
    else if (verbosity == 0)
        v = PQERRORS_DEFAULT;
    else // verbosity >= 1
        v = PQERRORS_VERBOSE;
    PQsetErrorVerbosity(db, v);

    return db;
}

void db_destroy(db_t *db) {
    PQfinish(db);
}

bool db_verify_api_key(db_t *db, api_key_t api_key) {

    (void)db;
    (void)api_key;
    return true;
}

serial_t db_get_user_id_by_email(db_t *db, const char email[static const EMAIL_LENGTH]) {
    // Oid p_oid = VARCHAROID;
    PGresult *res = PQexecParams(db, "select id from " SCHEMA_PACT "._compte where email = $1",
        1, NULL, &email, NULL, NULL, 1);

    serial_t user_id;

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "error: PQexecParams: %s", PQresultErrorMessage(res));
        user_id = 0;
    } else {
        user_id = ntohl(*((serial_t *)PQgetvalue(res, 0, 0)));
    }

    PQclear(res);
    return user_id;
}

serial_t db_get_user_id_by_pseudo(db_t *db, const char pseudo[static const PSEUDO_LENGTH]) {
    (void)db;
    (void)pseudo;
    return 1;
}
