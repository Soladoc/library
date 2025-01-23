#ifndef UUID_H
#define UUID_H

#include <stdbool.h>
#include <stdint.h>

/// @brief Version 4 UUID
typedef struct {
    uint8_t data[16];
} uuid4_t;

/// @brief Length of the canonical representation of a version 4 UUID
#define UUID4_REPR_LENGTH 36

/// @brief Generate the representation of a version 4 UUID.
/// @param uuid The UUID.
/// @param repr The representation buffer.
/// @return @p repr.
char *uuid4_repr(uuid4_t uuid, char repr[const UUID4_REPR_LENGTH]);

/// @brief Parse a version 4 UUID from its canonical representation.
/// @param uuid Mutated to the parsed UUID.
/// @param repr The string containing the representation.
/// @return `true` on success, `false` on failure.
/// @remark The syntax ABNF can be found at https://www.rfc-editor.org/rfc/rfc9562.html#section-4-5. Lowercase hex digits are allowed.
bool uuid4_from_repr(uuid4_t *uuid, char const repr[static const UUID4_REPR_LENGTH]);

#endif // UUID_H