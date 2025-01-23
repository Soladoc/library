#include "db.h"

serial_t db_get_user_id_by_email(const char email[static const EMAIL_LENGTH]) {
    (void) email;
    return 1;
}

serial_t db_get_user_id_by_pseudo(const char pseudo[static const PSEUDO_LENGTH]) {
    (void) pseudo;
    return 1;
}