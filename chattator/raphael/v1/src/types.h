/// @file
/// @author Raphaël
/// @brief General types - Standalone header
/// @date 23/01/2025

#ifndef TYPES_H
#define TYPES_H

#include <stdint.h>

#include "const.h"
#include "uuid.h"

typedef uuid4_t api_key_t;
typedef uint64_t token_t;
typedef uint32_t page_number_t;
/// @ref A Posrgres SERIAL primary key value (1..2^31-1)
typedef int32_t serial_t;

#define PASSWORD_HASH_LENGTH 255
#define EMAIL_LENGTH 319
#define PSEUDO_LENGTH 255

typedef char word_t[256];

typedef char password_hash_t[PASSWORD_HASH_LENGTH + 1], email_t[EMAIL_LENGTH + 1], pseudo_t[PSEUDO_LENGTH + 1];

typedef char action_name[8]; // keep the size as small as possible

#define X(str) _Static_assert(sizeof str <= sizeof(action_name), "buffer size too small for action name");
X_ACTION_NAMES
#undef X

typedef enum {
    user_kind_membre,
    user_kind_pro_prive,
    user_kind_pro_public,
} user_kind_t;

typedef struct {
    serial_t user_id;
    email_t email;
    word_t last_name, first_name, display_name;
    user_kind_t kind;
} user_t;

typedef enum {
    role_membre = 1 << 1,
    role_pro = 1 << 2,
    role_admin = 1 << 3,
} role_flags_t;

typedef struct {
    role_flags_t role;
    serial_t id;
} user_role_t;

#endif // TYPES_H
