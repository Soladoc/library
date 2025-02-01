#include "tchattator413/tests_tchattator413.h"
#include "tests.h"
#include <stdlib.h>
#include <tchattator413/cfg.h>
#include <tchattator413/db.h>

// #define DO_OBSERVE
#define OUT stdout

#define PGDB_PORT "5432"
#define DB_USER "postgres"
#define DB_NAME "sae413_test"
#define DB_ROOT_PASSWORD "postgres"
#define DB_HOST "localhost"


int main() {
    struct test t;
    bool success = true;
    cfg_t *cfg = cfg_defaults();
    db_t *db = db_connect(0, DB_HOST, PGDB_PORT, DB_NAME, DB_USER, DB_ROOT_PASSWORD);
    if (!db) return EX_NODB;

    server_t server = {};

#define test(new_test)                \
    do {                              \
        t = new_test;                 \
        success &= test_end(&t, OUT); \
    } while (0)

    test(test_uuid4());

#define CALL_TEST(name) test(test_tchattator413_##name(cfg, db, &server));
    X_TESTS(CALL_TEST)
#undef CALL_TEST

#ifdef DO_OBSERVE
    observe_put_role();
#endif

    cfg_destroy(cfg);
    db_destroy(db);
    server_destroy(&server);

    return success ? EXIT_SUCCESS : EXIT_FAILURE;
}
