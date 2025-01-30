#ifndef TESTS_H
#define TESTS_H

#include <stb_test.h>
#include <../src/db.h>
#include <../src/config.h>
#include <../src/server.h>
#include <stdbool.h>

struct test test_uuid4(void);

/// @brief Tchattator413 test: empty input
struct test test_tchattator413_zero(cfg_t *cfg, db_t *db, server_t *server);
/// @brief Tchattator413 test: whois of user 1
struct test test_tchattator413_admin_whois_1(cfg_t *cfg, db_t *db, server_t *server);

void observe_put_role(void);

#endif // TESTS_H
