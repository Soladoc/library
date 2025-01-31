#include "tests.h"
#include <stdlib.h>

// #define DO_OBSERVE
#define OUT stdout

int main() {
    struct test t;
    bool success = true;
    cfg_t *cfg = cfg_defaults();
    db_t *db = db_connect(0);
    server_t server = {};

#define test(new_test)                \
    do {                              \
        t = new_test;                 \
        success &= test_end(&t, OUT); \
    } while (0)

    test(test_uuid4());
    test(test_tchattator413_zero(cfg, db, &server));
    test(test_tchattator413_admin_whois_1(cfg, db, &server));
    test(test_tchattator413_admin_whois_neg1(cfg, db, &server));
    test(test_tchattator413_invalid_whois_1(cfg, db, &server));

#ifdef DO_OBSERVE
    observe_put_role();
#endif

    cfg_destroy(cfg);
    db_destroy(db);
    server_destroy(&server);

    return success ? EXIT_SUCCESS : EXIT_FAILURE;
}
