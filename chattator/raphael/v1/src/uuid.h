/// @file
/// @author Raphaël
/// @brief UUID library - Interface
/// @date 23/01/2025

#ifndef UUID_H
#define UUID_H

#include <stdint.h>
#include <stdio.h>

#include "errstatus.h"

/// @brief Version 4 UUID.
typedef struct {
    uint8_t data[16];
} uuid4_t;

/// @brief Length of the canonical representation of a version 4 UUID, excluding the null terminator.
#define UUID4_REPR_LENGTH 36

/// @brief Generate the representation of a version 4 UUID.
/// @param uuid The UUID.
/// @param repr The representation buffer.
/// @return @p repr.
/// @remark This function doesn't add a null terminator at the end of @p repr.
char *uuid4_repr(uuid4_t uuid, char repr[const UUID4_REPR_LENGTH]);

/// @brief Parse a version 4 UUID from its canonical representation.
/// @param out_uuid Mutated to the parsed UUID.
/// @param repr The string containing the representation.
/// @return The error status of the operation.
/// @remark The syntax ABNF can be found at https://www.rfc-editor.org/rfc/rfc9562.html#section-4-5. Lowercase hex digits are allowed.
errstatus_t uuid4_from_repr(uuid4_t *out_uuid, char const repr[static const UUID4_REPR_LENGTH]);

/// @brief Put the canonical representation of version 4 UUID.
/// @param uuid The UUID Version 4 to write.
/// @param stream The stream to write to.
void uuid4_put(uuid4_t uuid, FILE *stream);

/// @brief Are two version 4 UUIDs equal?
/// @param a The first UUID (lvalue).
/// @param b The second UUID (lvalue).
/// @return A boolean indicating whether @p a and @p b are equal.
#define uuid4_eq(a, b) (memcmp(&(a), &(b), sizeof(uuid4_t)) == 0)

/// @brief Create a new version 4 UUID from the specified values.
/// @retrun A new UUID Version 4.
#define uuid4_of(x1, x2, x3, x4, x5, x6, x7, x8, x9, x10, x11, x12, x13, x14, x15, x16) \
    (uuid4_t) {                                                                         \
        .data = {                                                                       \
            x1,                                                                         \
            x2,                                                                         \
            x3,                                                                         \
            x4,                                                                         \
            x5,                                                                         \
            x6,                                                                         \
            x7,                                                                         \
            x8,                                                                         \
            x9,                                                                         \
            x10,                                                                        \
            x11,                                                                        \
            x12,                                                                        \
            x13,                                                                        \
            x14,                                                                        \
            x15,                                                                        \
            x16,                                                                        \
        }                                                                               \
    }

#endif // UUID_H
