/// @file
/// @author Raphaël
/// @brief Tchattator413 dynamic server state - Implemtnation
/// @date 29/01/2025

#include "server.h"
#include "types.h"

#include <stb_ds.h>

void server_destroy(server_t *server) {
    hmfree(server->turnstile);
    hmfree(server->sessions);
}

bool server_turnstile_rate_limit(server_t *server, serial_t user_id, cfg_t *cfg) {
    time_t const t = time(NULL);

    int i = hmgeti(server->turnstile, user_id);
    if (i == -1) {
        hmput(server->turnstile, user_id,
            ((user_stats_t) {
                .last_request_at = t,
                .n_requests_h = 1,
                .n_requests_m = 1,
            }));
        return true;
    }

    user_stats_t *stats = &server->turnstile[i].value;

    time_t time_since_last_request = t - stats->last_request_at;
    stats->last_request_at += time_since_last_request;

    if (time_since_last_request > 60) stats->n_requests_m = 0;
    if (time_since_last_request > 3600) stats->n_requests_h = 0;

    ++stats->n_requests_m;
    ++stats->n_requests_h;

    return stats->n_requests_m < config_rate_limit_m(cfg) && stats->n_requests_h < config_rate_limit_h(cfg);
}

token_t server_login(server_t *server, serial_t user_id) {
    // our key: (user_id, time)
    // we won't allow the same user to login twice in the same second. That will be a collision.
    // merge user_id (high bits) and time (low 32 bits)
    time_t const t = time(NULL);
    token_t token = ((long)user_id << 32) + (int32_t)t;

    if (hmgeti(server->turnstile, user_id) != -1) return 0;
    hmput(server->sessions, token, user_id);

    return token;
}

bool server_logout(server_t *server, token_t token) {
    return hmdel(server->sessions, token);
}

serial_t server_verify_token(server_t *server, token_t token) {
    return hmget(server->sessions, token); // the default value is 0
}
