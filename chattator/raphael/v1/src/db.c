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

#define pq_recv_l(type, res, row, col) ((type)(ntohl(*((type *)PQgetvalue((res), (row), (col))))))
#define pq_send_l(val) htonl(val)

#define put_pq_error(db) put_error("database: %s", PQerrorMessage(db))
#define put_pq_result_error(result) put_error("database: %s", PQresultErrorMessage(result))

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
        put_pq_error(db);
        PQfinish(db);
        return NULL;
    }

    return db;
}

void db_destroy(db_t *db) {
    PQfinish(db);
}

errstatus_t db_verify_api_key(db_t *db, api_key_t api_key) {

    (void)db;
    (void)api_key;
    return errstatus_ok;
}

serial_t db_get_user_id_by_email(db_t *db, const char *email) {
    PGresult *result = PQexecParams(db, "select id from " SCHEMA_PACT "._compte where email = $1",
        1, NULL, &email, NULL, NULL, 1);

    serial_t res;

    if (PQresultStatus(result) != PGRES_TUPLES_OK) {
        put_pq_result_error(result);
        res = errstatus_handled;
    } else if (PQntuples(result) == 0) {
        put_error("cannot find user by email: %s\n", email);
        res = errstatus_handled;
    } else {
        res = pq_recv_l(serial_t, result, 0, 0);
    }

    PQclear(result);
    return res;
}

serial_t db_get_user_id_by_pseudo(db_t *db, const char *pseudo) {
    PGresult *result = PQexecParams(db, "select id from " SCHEMA_PACT "._membre where pseudo=$1",
        1, NULL, &pseudo, NULL, NULL, 1);

    serial_t res;

    if (PQresultStatus(result) != PGRES_TUPLES_OK) {
        put_pq_result_error(result);
        res = errstatus_handled;
    } else if (PQntuples(result) == 0) {
        put_error("cannot find user by pseudo: %s\n", pseudo);
        res = errstatus_handled;
    } else {
        res = pq_recv_l(serial_t, result, 0, 0);
    }

    PQclear(result);

    return res;
}

errstatus_t db_get_user(db_t *db, user_t *user) {
    char const *p_value[1];
    int p_length[1], p_format[1];

    uint32_t arg = pq_send_l(user->user_id);
    p_value[0] = (char *)&arg;
    p_length[0] = sizeof arg;
    p_format[0] = 1;
    PGresult *result = PQexecParams(db, "select kind, id, email, nom, prenom, display_name from " SCHEMA_TCHATTATOR ".user where id=$1",
        1, NULL, p_value, p_length, p_format, 1);

    errstatus_t res;

    if (PQresultStatus(result) != PGRES_TUPLES_OK) {
        put_pq_result_error(result);
        res = errstatus_handled;
    } else if (PQntuples(result) == 0) {
        put_error("cannot find user by id: %d\n", user->user_id);
        res = errstatus_handled;
    } else {
        user->kind = pq_recv_l(enum user_kind, result, 0, 0);
        assert(user->user_id == pq_recv_l(serial_t, result, 0, 1));
        strncpy(user->email, PQgetvalue(result, 0, 2), sizeof user->email);
        strncpy(user->last_name, PQgetvalue(result, 0, 3), sizeof user->last_name);
        strncpy(user->first_name, PQgetvalue(result, 0, 4), sizeof user->first_name);
        strncpy(user->display_name, PQgetvalue(result, 0, 5), sizeof user->display_name);
        res = errstatus_ok;
    }

    PQclear(result);
    return res;
}
