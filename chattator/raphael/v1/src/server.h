/// @file
/// @author Raphaël
/// @brief Tchattator413 dynamic server state - Interface
/// @date 29/01/2025

#ifndef SERVER_STATE_H
#define SERVER_STATE_H

#include "config.h"
#include <stdbool.h>
typedef void* server_t;

server_t server_create();
void server_destroy(server_t *server);

/// @brief Checks and increments the rate limit for the specified user.
/// @param server The server.
/// @param user_id The ID of the user performing the request.
/// @return @c true The turnstile passes (the rate limit hasn't been reached)
/// @return @c false The turnstile blocks (the rate limit has been reached). An error has been put.
bool server_turnstile_rate_limit(server_t *server, serial_t user_id, cfg_t *cfg);

/// @brief Creates a new session
/// @param server The server.
/// @param out_token Assigned to the session token.
/// @param user_id The ID of the user to login.
/// @param password_hash The hash of the password to login with
/// @return @c true The session was created successfully.
/// @return @c false The password was incorrect. @p is untouched.
bool server_login(server_t *server, token_t *out_token, serial_t user_id, char const password_hash[static PASSWORD_HASH_LENGTH]);

#endif // SERVER_STATE_H