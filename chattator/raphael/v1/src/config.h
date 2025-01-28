#ifndef CONFIG_H
#define CONFIG_H

#include "db.h"
#include "types.h"

/// @brief An opaque handle to a configuration object.
typedef struct config config_t;

/// @brief Load configuration from a file.
/// @param filename The filename to read the config from.
/// @return A new configuration object.
config_t *config_from_file(char const *filename);

/// @brief Load the default configuration.
/// @return A new configuration object
config_t *config_defaults(void);

/// @brief Verify if an API key is valid.
/// 
/// @param cfg The configuration.
/// @param api_key The apik key to check.
/// @param db The database connection.
/// @return The ID of the user who own this API key.
/// @return @ref min_errstatus - 1 The API key is the admin API key.
/// @return @ref errstatus_handled if an error has occured and was handled.
/// @return @ref errstatus_error if the API key is invalid.
/// @return @ref Any value > 0 for the ID of the user this API keys belongs to.
struct { user_role_t user_role; errstatus_t error; } config_verify_api_key(config_t *cfg, api_key_t api_key, db_t *db);
// todo: what does this return for admin? admin has a null id.s

/// @brief Destroy a configuration.
/// @param cfg The configuration to destroy. No-op if @c NULL.
void config_destroy(config_t *cfg);

#endif // CONFIG_H
