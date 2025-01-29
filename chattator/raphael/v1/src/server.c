/// @file
/// @author Raphaël
/// @brief Tchattator413 dynamic server state - Implemtnation
/// @date 29/01/2025

#include <time.h>

#include "../lib/stb_ds.h"
#include "server.h"
#include "types.h"

typedef struct {
    /// @brief Timestamp of the last request.
    time_t last_request_at;
    /// @brief Authentication token.
    /// @remark @c 0 for no token.
    token_t token;
    /// @brief Number of requests performed since an hour.
    int n_requests_h;
    /// @brief Number of requests performed since a minute.
    int n_requests_m;
} session_t;

typedef struct {
    serial_t key;
    session_t value;
} session_entry_t;

server_t server_create()
{
    return NULL;
}

void server_destroy(server_t *server)
{
    hmfree(*server);
}

bool server_turnstile_rate_limit(server_t *server, serial_t user_id, cfg_t *cfg)
{
    session_entry_t **serv = (session_entry_t**)server;
    session_t *sess = &hmgetp(*serv, user_id)->value;

    // is user's first request <=> sess->last_request_at == 0 (unless we're the 01-01-1970 at 00:00:00)

    time_t time_since_last_request = time(NULL) - sess->last_request_at;
    sess->last_request_at += time_since_last_request;

    if (time_since_last_request > 60) sess->n_requests_m = 0;
    if (time_since_last_request > 3600) sess->n_requests_h = 0;

    ++sess->n_requests_m;
    ++sess->n_requests_h;

    return sess->n_requests_m < config_rate_limit_m(cfg) && sess->n_requests_h < config_rate_limit_h(cfg);
}

bool server_login(server_t *server, token_t *out_token, serial_t user_id, char const password_hash[static const PASSWORD_HASH_LENGTH])
{

}
