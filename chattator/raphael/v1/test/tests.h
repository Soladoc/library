#ifndef TESTS_H
#define TESTS_H

#include <stb_test.h>
#include <../src/db.h>
#include <../src/cfg.h>
#include <../src/server.h>
#include <stdbool.h>

struct test test_uuid4(void);

// Tchattator413 test naming convention:
// test_tchattator413_<ROLE>_<ACTION>_<WITH...>
// ROLE is
//  - admin: administrator
//  - pro: professional
//  - member: membre
//  - invalid: invalid token / api key
// ACTION is an action name
// WITH are the action arguments, one or more, separated by '_'

/// @brief Tchattator413 test: empty input (0 actions)
struct test test_tchattator413_zero(cfg_t *cfg, db_t *db, server_t *server);

struct test test_tchattator413_admin_whois_1(cfg_t *cfg, db_t *db, server_t *server);
struct test test_tchattator413_admin_whois_neg1(cfg_t *cfg, db_t *db, server_t *server);

struct test test_tchattator413_invalid_whois_1(cfg_t *cfg, db_t* db, server_t *server);
struct test test_tchattator413_invalid_login(cfg_t *cfg, db_t *db, server_t *server);

void observe_put_role(void);

#endif // TESTS_H
