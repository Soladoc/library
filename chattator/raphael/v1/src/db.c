/// @file
/// @author Raphaël
/// @brief DAL - Implementation
/// @date 23/01/2025

#include <assert.h>
#include <netinet/in.h>
#include <postgresql/libpq-fe.h>

#include <stdlib.h>

#include "db.h"
#include "util.h"

#define SCHEMA_PACT "pact"
#define SCHEMA_TCHATTATOR "tchattator"

#define conn_param(param) coalesce(getenv(#param), STR(param))

#define pq_get_l(type, res, row, col) ((type)(ntohl(*((type *)PQgetvalue((res), (row), (col))))))

db_t *db_connect(int verbosity) {
    PGconn *db = PQsetdbLogin(
        conn_param(DB_HOST),
        conn_param(PGDB_PORT),
        NULL, NULL,
        conn_param(DB_NAME),
        conn_param(DB_USER),
        conn_param(DB_ROOT_PASSWORD));

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

    if (PQstatus(db) != CONNECTION_OK) {
        fprintf(stderr, "error: PQsetdbLogin: %s\n", PQerrorMessage(db));
        PQfinish(db);
        return NULL;
    }

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

serial_t db_get_user_id_by_email(db_t *db, const char *email) {
    PGresult *res = PQexecParams(db, "select id from " SCHEMA_PACT "._compte where email = $1",
        1, NULL, &email, NULL, NULL, 1);

    serial_t user_id;

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "error: PQexecParams: %s\n", PQresultErrorMessage(res));
        user_id = 0;
    } else if (PQntuples(res) == 0) {
        fprintf(stderr, "error: cannot find user of email %s\n", email);
        user_id = 0;
    } else {
        user_id = pq_get_l(serial_t, res, 0, 0);
    }

    PQclear(res);
    return user_id;
}

serial_t db_get_user_id_by_pseudo(db_t *db, const char *pseudo) {
    PGresult *res = PQexecParams(db, "select id from " SCHEMA_PACT "._membre where pseudo=$1",
        1, NULL, &pseudo, NULL, NULL, 1);

    serial_t user_id;

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "error: PQexecParams: %s\n", PQresultErrorMessage(res));
        user_id = 0;
    } else if (PQntuples(res) == 0) {
        fprintf(stderr, "error: cannot find user of pseudo %s\n", pseudo);
        user_id = 0;
    } else {
        user_id = pq_get_l(serial_t, res, 0, 0);
    }

    PQclear(res);

    return user_id;
}

bool db_get_user(db_t *db, user_t *user) {
    void *arg = &user->user_id;
    PGresult *res = PQexecParams(db, "select kind, id, email, nom, prenom, display_name from " SCHEMA_TCHATTATOR ".user where id=$1",
        1, NULL, (const char **)&arg, NULL, NULL, 1);

    bool ok;

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "error: PQexecParams: %s\n", PQresultErrorMessage(res));
        ok = false;
    } else if (PQntuples(res) == 0) {
        fprintf(stderr, "error: cannot find user of id %d\n", user->user_id);
        ok = false;
    } else {
        user->kind = pq_get_l(enum user_kind, res, 0, 0);
        assert(user->user_id == pq_get_l(serial_t, res, 0, 1));
        strncpy(user->email, PQgetvalue(res, 0, 2), sizeof user->email);
        strncpy(user->last_name, PQgetvalue(res, 0, 3), sizeof user->last_name);
        strncpy(user->first_name, PQgetvalue(res, 0, 4), sizeof user->first_name);
        strncpy(user->display_name, PQgetvalue(res, 0, 5), sizeof user->display_name);
        ok = true;
    }

    PQclear(res);
    return ok;
}
