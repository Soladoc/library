#include <postgresql/libpq-fe.h>
#include <stdlib.h>

#include "db.h"
#include "util.h"

#define conn_param(param) coalesce(getenv(#param), STR(param))

db_t *db_connection_connect(void) {
    char conninfo[256];
    snprintf(conninfo, sizeof conninfo,
        "host=%s port=%s dbname=%s user=%s password=%s",
        conn_param(DB_HOST),
        conn_param(PGDB_PORT),
        conn_param(DB_NAME),
        conn_param(DB_USER),
        conn_param(DB_ROOT_PASSWORD));
    PGconn *db = PQconnectdb(conninfo);

    if (PQstatus(db) != CONNECTION_OK) {
        fprintf(stderr, "error: failed to connect to db: %s\n", PQerrorMessage(db));
        PQfinish(db);
        return NULL;
    }

    return db;
}

void db_connection_destroy(db_t *db) {
    PQfinish(db);
}

bool db_verify_api_key(db_t *db, api_key_t api_key) {

    (void)db;
    (void)api_key;
    return true;
}

serial_t db_get_user_id_by_email(db_t *db, const char email[static const EMAIL_LENGTH]) {
    (void)db;
    (void)email;
    return 1;
}

serial_t db_get_user_id_by_pseudo(db_t *db, const char pseudo[static const PSEUDO_LENGTH]) {
    (void)db;
    (void)pseudo;
    return 1;
}
