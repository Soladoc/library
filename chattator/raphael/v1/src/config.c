#include <errno.h>
#include <json-c/json.h>
#include <stdlib.h>
#include <string.h>

#include "config.h"
#include "util.h"

#define DEFAULT_LOG_STREAM stderr

struct config {
    uuid4_t admin_api_key;
    FILE *log_file;
    int max_msg_length;
    int page_inbox;
    int page_outbox;
    int rate_limit_m;
    int rate_limit_h;
    int block_for;
    int backlog;
    short port;
};

cfg_t *config_defaults(void) {
    cfg_t *cfg = malloc(sizeof *cfg);
    if (!cfg) fail_malloc();
    cfg->max_msg_length = 1000;
    cfg->page_inbox = 20;
    cfg->page_outbox = 20;
    cfg->rate_limit_m = 90;
    cfg->rate_limit_h = 12;
    cfg->block_for = 86400;
    // ed33c143-5752-4543-a821-00a187955a28
    cfg->admin_api_key = uuid4_of(0xed, 0x33, 0xc1, 0x43, 0x57, 0x52, 0x45, 0x43, 0xa8, 0x21, 0x00, 0xa1, 0x87, 0x95, 0x5a, 0x28);
    cfg->port = 4113;
    cfg->log_file = DEFAULT_LOG_STREAM;
    cfg->backlog = 1;
    return cfg;
}

void config_destroy(cfg_t *cfg) {
    if (cfg->log_file != DEFAULT_LOG_STREAM) fclose(cfg->log_file);
    free(cfg);
}

cfg_t *config_from_file(char const *filename) {
    json_object *c = json_object_from_file(filename), *k;

    cfg_t *cfg = config_defaults();

    if ((k = json_object_object_get(c, "max_msg_length")) && json_object_is_type(k, json_type_int)) cfg->max_msg_length = json_object_get_int(k);
    if ((k = json_object_object_get(c, "page_inbox")) && json_object_is_type(k, json_type_int)) cfg->page_inbox = json_object_get_int(k);
    if ((k = json_object_object_get(c, "page_outbox")) && json_object_is_type(k, json_type_int)) cfg->page_outbox = json_object_get_int(k);
    if ((k = json_object_object_get(c, "rate_limit_m")) && json_object_is_type(k, json_type_int)) cfg->rate_limit_m = json_object_get_int(k);
    if ((k = json_object_object_get(c, "rate_limit_h")) && json_object_is_type(k, json_type_int)) cfg->rate_limit_h = json_object_get_int(k);
    if ((k = json_object_object_get(c, "block_for")) && json_object_is_type(k, json_type_int)) cfg->block_for = json_object_get_int(k);
    if ((k = json_object_object_get(c, "port")) && json_object_is_type(k, json_type_int)) cfg->port = json_object_get_int(k);
    if ((k = json_object_object_get(c, "backlog")) && json_object_is_type(k, json_type_int)) cfg->backlog = json_object_get_int(k);

    if ((k = json_object_object_get(c, "admin_api_key")) && json_object_is_type(k, json_type_string)) {
        if (errstatus_error == uuid4_from_repr(&cfg->admin_api_key, json_object_get_string(k))) {
            put_error("configuration error: invalid admin_api_key: %s", json_object_get_string(k));
            free(cfg);
            return NULL;
        }
    }
    if ((k = json_object_object_get(c, "log_file")) && json_object_is_type(k, json_type_int)) {
        cfg->log_file = fopen(json_object_get_string(k), "a");
        if (!cfg->log_file) {
            put_error("configuration error: could not open log file: %s", strerror(errno));
            free(cfg);
            return NULL;
        }
    }

    json_object_put(c);

    return cfg;
}

serial_t config_verify_api_key(config_verify_api_key_t *result, cfg_t *cfg, api_key_t api_key, db_t *db) {
    if (uuiq4_equal(api_key, cfg->admin_api_key)) {
        result->user_role = role_admin;
        result->user_id = 0;
        return errstatus_ok;
    }
    db_verify_user_api_key_t db_result;
    errstatus_t err = db_verify_user_api_key(&db_result, db, api_key);
    if (err == errstatus_ok) {
        result->user_id = db_result.user_id;
        switch (db_result.user_kind) {
        case user_kind_membre: result->user_role = role_membre; break;
        case user_kind_pro_prive: [[fallthrough]];
        case user_kind_pro_public: result->user_role = role_pro; break;
        }
    }
    return err;
}

int config_max_msg_length(cfg_t *cfg) {
    return cfg->max_msg_length;
}
