/// @file
/// @author Raphaël
/// @brief Tchatator413 dynamic server state - Interface
/// @date 29/01/2025

#ifndef SERVER_STATE_H
#define SERVER_STATE_H

#include "cfg.h"
#include "types.h"
#include <stdbool.h>
#include <time.h>

typedef struct {
    /// @brief Timestamp of the last request.
    time_t last_request_at;
    /// @brief Number of requests performed since an hour.
    int n_requests_h;
    /// @brief Number of requests performed since a minute.
    int n_requests_m;
} user_stats_t;

/// @brief Opaque type handle representing a server instance.
typedef struct server server_t;

/// @brief A bit flags enumeration that configures the server.
typedef enum {
    server_regular, ///< @brief Regular settings.
    server_rate_limiting = 1 << 0, ///< @brief Enable rate limiting.
} server_flags_t;

/// @brief Creates a new server instance with the specified configuration flags.
/// @param flags The configuration flags for the server.
/// @return A new server instance.
server_t *server_create(server_flags_t flags);

/// @brief Destroys the specified server instance.
/// @param server The server instance to destroy.
void server_destroy(server_t *server);

/// @brief Checks and increments the rate limit for the specified user.
/// @param server The server.
/// @param user_id The ID of the user performing the request.
/// @param cfg The configuration.
/// @return @c 0 The turnstile passes (the rate limit hasn't been reached)
/// @return > @c 0 The turnstile blocks (the rate limit has been reached). An error has been put. The return value is time of the next allowed request.
time_t server_turnstile_rate_limit(server_t *server, serial_t user_id, cfg_t *cfg);

/// @brief Creates a new session, logging in an user.
/// @param server The server.
/// @param user_id The ID of the user to login.
/// @return The new session token.
/// @return @c 0 if the the session could not be created. This happens if the same user tries logs in twice in the same second.
token_t server_login(server_t *server, serial_t user_id);

/// @brief Deletes a session, logging out an user
/// @param server The server.
/// @param token The session token to invalidate.
/// @return @c true on successful log out.
/// @return @c false if the token is invalid.
bool server_logout(server_t *server, token_t token);

/// @brief Verifies a token, returning its owning user ID.
/// @param server The server.
/// @param token The session token to verify.
/// @return @c -1 if the session token is invalid.
/// @return The ID of the user that owns @p token if it is valid.
serial_t server_verify_token(server_t *server, token_t token);

#endif // SERVER_STATE_H
