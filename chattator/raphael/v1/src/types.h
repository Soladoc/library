#ifndef TYPES_H
#define TYPES_H

#include <stdint.h>

#include "const.h"
#include "uuid.h"

typedef uuid4_t api_key_t;
typedef uint64_t token_t;
typedef uint32_t serial_t, page_number_t;
typedef char password_hash_t[256], pseudo_t[256], email_t[320];

enum account_key_tag {
    account_key_tag_id,
    account_key_tag_pseudo,
    account_key_tag_email,
};

struct account_key {
    enum account_key_tag tag;
    union {
        serial_t id;
        pseudo_t pseudo;
        email_t email;
    } value;
};

typedef char action_name[8]; // keep the size as small as possible

#define X(str) _Static_assert(sizeof str <= sizeof(action_name), "buffer size too small for action name");
X_ACTION_NAMES
#undef X

#endif // TYPES_H
