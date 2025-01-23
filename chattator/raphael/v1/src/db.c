#include "db.h"

bool db_connection_connect(struct db_connection *db) {
    (void)db;
    return true;
}

serial_t db_get_user_id_by_email(struct db_connection *db, const char email[static const EMAIL_LENGTH]) {
    (void)db;
    (void)email;
    return 1;
}

serial_t db_get_user_id_by_pseudo(struct db_connection *db, const char pseudo[static const PSEUDO_LENGTH]) {
    (void)db;
    (void)pseudo;
    return 1;
}

void db_connection_destroy(struct db_connection *db) {
    (void)db;
}
