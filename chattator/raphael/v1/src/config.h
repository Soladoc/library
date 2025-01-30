/// @file
/// @author Raphaël
/// @brief Tchattator413 server configuration - Interface
/// @date 29/01/2025

#ifndef CONFIG_H
#define CONFIG_H

#include "types.h"

/// @brief An opaque handle to a configuration object.
typedef struct cfg cfg_t;

/// @brief Load the default configuration.
/// @return A new configuration object
cfg_t *config_defaults(void);

/// @brief Destroy a configuration.
/// @param cfg The configuration to destroy. No-op if @c NULL.
void config_destroy(cfg_t *cfg);

/// @brief Load configuration from a file.
/// @param filename The filename to read the config from.
/// @return A new configuration object.
cfg_t *config_from_file(char const *filename);

/// @brief Dump a configuration to standard output.
/// @param cfg The configuration to dump.
void config_dump(cfg_t const *cfg);

/// @brief Get the configuration admin_api_key.
/// @param cfg Configuration
/// @return Pointer to the che configuration admin_api_key.
uuid4_t const *config_admin_api_key(cfg_t const *cfg);
/// @brief Get the configuration log_file.
/// @param cfg Configuration
/// @return the configuration log_file.
FILE * config_log_file(cfg_t const *cfg);
/// @brief Get the configuration max_msg_length.
/// @param cfg Configuration
/// @return the configuration max_msg_length.
int config_max_msg_length(cfg_t const *cfg);
/// @brief Get the configuration page_inbox.
/// @param cfg Configuration
/// @return the configuration page_inbox.
int config_page_inbox(cfg_t const *cfg);
/// @brief Get the configuration page_outbox.
/// @param cfg Configuration
/// @return the configuration page_outbox.
int config_page_outbox(cfg_t const *cfg);
/// @brief Get the configuration rate_limit_m.
/// @param cfg Configuration
/// @return the configuration rate_limit_m.
int config_rate_limit_m(cfg_t const *cfg);
/// @brief Get the configuration rate_limit_h.
/// @param cfg Configuration
/// @return the configuration rate_limit_h.
int config_rate_limit_h(cfg_t const *cfg);
/// @brief Get the configuration block_for.
/// @param cfg Configuration
/// @return the configuration block_for.
int config_block_for(cfg_t const *cfg);
/// @brief Get the configuration backlog.
/// @param cfg Configuration
/// @return the configuration backlog.
int config_backlog(cfg_t const *cfg);
/// @brief Get the configuration port.
/// @param cfg Configuration
/// @return the configuration port.
uint16_t config_port(cfg_t const *cfg);

#endif // CONFIG_H
