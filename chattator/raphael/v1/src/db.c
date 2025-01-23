#include <postgresql/libpq-fe.h>
#include <stdlib.h>

#include "db.h"
#include "util.h"

#define CONNINFO_HOSTNAME_LENGTH 64

#define CONNINFO_FMT_HOST "%." STR(CONNINFO_HOSTNAME_LENGTH) "s"

#define CONNINFO "host=" CONNINFO_FMT_HOST " port=" STR(PGDB_PORT) " dbname=" STR(DB_NAME) " user=" STR(DB_USER) " password=" STR(DB_ROOT_PASSWORD)

#define CONNINFO_LENGTH (STRLEN(CONNINFO) - STRLEN(CONNINFO_FMT_HOST) + CONNINFO_HOSTNAME_LENGTH)

db_t *db_connection_connect(char const *host) {
    char conninfo[CONNINFO_LENGTH + 1];
    snprintf(conninfo, sizeof conninfo, CONNINFO, host);
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
