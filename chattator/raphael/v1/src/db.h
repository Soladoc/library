#ifndef DB_H
#define DB_H

#include "types.h"

struct db_connection {
    void *cursor;
};

bool db_connection_connect(struct db_connection *db);

void db_connection_destroy(struct db_connection *db);

serial_t db_get_user_id_by_email(struct db_connection *db, const char email[static const EMAIL_LENGTH]);

serial_t db_get_user_id_by_pseudo(struct db_connection *db, const char pseudo[static const PSEUDO_LENGTH]);

#endif // DB_H
