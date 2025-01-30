#ifndef TESTS_H
#define TESTS_H

#include <stb_test.h>
#include <../src/db.h>
#include <../src/config.h>
#include <../src/server.h>
#include <stdbool.h>

struct test test_uuid(void);
struct test test_1(cfg_t *cfg, db_t *db, server_t *server);

void observe_put_role(void);

#endif // TESTS_H
