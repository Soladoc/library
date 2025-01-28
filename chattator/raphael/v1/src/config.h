#ifndef CONFIG_H
#define CONFIG_H

#include "db.h"
#include "types.h"

/// @brief An opaque handle to a configuration object.
typedef struct config cfg_t;

/// @brief Load configuration from a file.
/// @param filename The filename to read the config from.
/// @return A new configuration object.
cfg_t *config_from_file(char const *filename);

/// @brief Load the default configuration.
/// @return A new configuration object
cfg_t *config_defaults(void);

/// @brief Result of @ref config_verify_api_key.
typedef struct {
    /// @brief The roles of the user.
    role_flags_t user_role;
    /// @brief The ID of the user or @c 0 if for the adminsitrator.
    serial_t user_id;
} config_verify_api_key_t;

/// @brief Verify if an API key is valid.
/// 
/// @param result Mutated to the result.
/// @param cfg The configuration.
/// @param api_key The apik key to check.
/// @param db The database connection.
/// @return The ID of the user who own this API key.
/// @return The error status. If an error occured, the value of @p result is untouched.
serial_t config_verify_api_key(config_verify_api_key_t *result, cfg_t *cfg, api_key_t api_key, db_t *db);

/// @brief Destroy a configuration.
/// @param cfg The configuration to destroy. No-op if @c NULL.
void config_destroy(cfg_t *cfg);

int config_max_msg_length(cfg_t *cfg);

#endif // CONFIG_H
