#ifndef DB_H
#define DB_H

#include "types.h"

typedef void db_t;

db_t *db_connection_connect(void);

void db_connection_destroy(db_t *db);

bool db_verify_api_key(db_t *db, api_key_t api_key);

serial_t db_get_user_id_by_email(db_t *db, const char email[static const EMAIL_LENGTH]);

serial_t db_get_user_id_by_pseudo(db_t *db, const char pseudo[static const PSEUDO_LENGTH]);

#endif // DB_H
