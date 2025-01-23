#ifndef DB_H
#define DB_H

#include "types.h"

serial_t db_get_account_id_by_email(const char email[static const EMAIL_LENGTH]);

serial_t db_get_account_id_by_pseudo(const char pseudo[static const PSEUDO_LENGTH]);

#endif // DB_H