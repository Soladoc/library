/// @file
/// @author Raphaël
/// @brief DAL - Implementation
/// @date 23/01/2025

#include <assert.h>
#include <netinet/in.h>
#include <postgresql/libpq-fe.h>

#include <stdlib.h>

#include <bcrypt/bcrypt.h>
#include "db.h"
#include "util.h"

#define TBL_USER "tchattator.user"
#define TBL_MEMBRE "pact.membre"

#define conn_param(param) coalesce(getenv(#param), STR(param))

#define pq_recv_l(type, res, row, col) ((type)(ntohl(*((type *)PQgetvalue((res), (row), (col))))))
#define pq_send_l(val) htonl(val)

#define putln_error_pq(db) put_error("database: %s\n", PQerrorMessage(db))
#define putln_error_pq_result(result) put_error("database: %s\n", PQresultErrorMessage(result))

static inline role_flags_t user_kind_to_role(user_kind_t kind) {
    switch (kind) {
    case user_kind_membre: return role_membre;
    case user_kind_pro_prive: [[fallthrough]];
    case user_kind_pro_public: return role_pro;
    }
    unreachable();
}

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
        putln_error_pq(db);
        PQfinish(db);
        return NULL;
    }

    return db;
}

void db_destroy(db_t *db) {
    PQfinish(db);
}

errstatus_t db_verify_user_api_key(db_t *db, user_identity_t *out_user, api_key_t api_key) {
    char api_key_repr[UUID4_REPR_LENGTH + 1];
    uuid4_repr(api_key, api_key_repr);
    api_key_repr[UUID4_REPR_LENGTH] = '\0';
    char const *arg = api_key_repr;
    PGresult *pg_result = PQexecParams(db, "select kind, id from " TBL_USER " where api_key = $1",
        1, NULL, &arg, NULL, NULL, 1);

    errstatus_t res;
    if (PQresultStatus(pg_result) != PGRES_TUPLES_OK) {
        putln_error_pq_result(pg_result);
        res = errstatus_handled;
    } else if (PQntuples(pg_result) == 0) {
        res = errstatus_error;
    } else {
        out_user->role = user_kind_to_role(pq_recv_l(user_kind_t, pg_result, 0, 0));
        out_user->id = pq_recv_l(serial_t, pg_result, 0, 1);
        res = errstatus_ok;
    }
    PQclear(pg_result);
    return res;
}

int db_get_user_role(db_t *db, serial_t user_id) {
    uint32_t arg = pq_send_l(user_id);
    char const *p_value = (char *)&arg;
    int p_length = sizeof arg;
    int p_format = 1;
    PGresult *result = PQexecParams(db, "select kind from " TBL_USER " where id=$1",
        1, NULL, &p_value, &p_length, &p_format, 1);

    int res;

    if (PQresultStatus(result) != PGRES_TUPLES_OK) {
        putln_error_pq_result(result);
        res = errstatus_handled;
    } else if (PQntuples(result) == 0) {
        put_error("cannot find user by id: %d\n", user_id);
        res = errstatus_handled;
    } else {
        res = user_kind_to_role(pq_recv_l(user_kind_t, result, 0, 0));
    }

    PQclear(result);
    return res;
}

serial_t db_get_user_id_by_email(db_t *db, const char *email) {
    PGresult *result = PQexecParams(db, "select kind, id from " TBL_USER " where email = $1",
        1, NULL, &email, NULL, NULL, 1);

    serial_t res;

    if (PQresultStatus(result) != PGRES_TUPLES_OK) {
        putln_error_pq_result(result);
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
    PGresult *result = PQexecParams(db, "select id from " TBL_MEMBRE " where pseudo=$1",
        1, NULL, &pseudo, NULL, NULL, 1);

    serial_t res;

    if (PQresultStatus(result) != PGRES_TUPLES_OK) {
        putln_error_pq_result(result);
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
    uint32_t arg = pq_send_l(user->user_id);
    char const *p_value = (char *)&arg;
    int p_length = sizeof arg;
    int p_format = 1;
    PGresult *result = PQexecParams(db, "select kind, id, email, nom, prenom, display_name from " TBL_USER " where id=$1",
        1, NULL, &p_value, &p_length, &p_format, 1);

    errstatus_t res;

    if (PQresultStatus(result) != PGRES_TUPLES_OK) {
        putln_error_pq_result(result);
        res = errstatus_handled;
    } else if (PQntuples(result) == 0) {
        put_error("cannot find user by id: %d\n", user->user_id);
        res = errstatus_handled;
    } else {
        user->kind = pq_recv_l(user_kind_t, result, 0, 0);
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

errstatus_t db_check_password(db_t *db, serial_t user_id, char const *password) {
    uint32_t arg = pq_send_l(user_id);
    char const *p_value = (char *)&arg;
    int p_length = sizeof arg;
    int p_format = 1;
    PGresult *result = PQexecParams(db, "select mdp_hash from " TBL_USER " where id=$1",
        1, NULL, &p_value, &p_length, &p_format, 1);

    errstatus_t res;

    if (PQresultStatus(result) != PGRES_TUPLES_OK) {
        putln_error_pq_result(result);
        res = errstatus_handled;
    } else if (PQntuples(result) == 0) {
        put_error("cannot find user by id: %d\n", user_id);
        res = errstatus_handled;
    } else {
        switch (bcrypt_checkpw(password, PQgetvalue(result, 0, 0))) {
        case -1: errno_exit("bcrypt_checkpw");
        case 0: return errstatus_error;
        }
        res = errstatus_ok;
    }

    PQclear(result);
    return res;
}
