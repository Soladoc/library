#include <errno.h>
#include <json-c/json.h>
#include <stdlib.h>
#include <string.h>

#include "config.h"
#include "json-helpers.h"
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
    uint16_t port;
    /// @remark Can be @c NULL if log_file is a standard stream.
    char *log_file_name;
};

cfg_t *config_defaults(void) {
    cfg_t *cfg = malloc(sizeof *cfg);
    if (!cfg) errno_exit("malloc");

    // ed33c143-5752-4543-a821-00a187955a28
    cfg->admin_api_key = uuid4_of(0xed, 0x33, 0xc1, 0x43, 0x57, 0x52, 0x45, 0x43, 0xa8, 0x21, 0x00, 0xa1, 0x87, 0x95, 0x5a, 0x28);

    cfg->log_file = DEFAULT_LOG_STREAM;
    cfg->log_file_name = NULL;

    cfg->backlog = 1;
    cfg->block_for = 86400;
    cfg->max_msg_length = 1000;
    cfg->page_inbox = 20;
    cfg->page_outbox = 20;
    cfg->port = 4113;
    cfg->rate_limit_h = 12;
    cfg->rate_limit_m = 90;
    return cfg;
}

void config_destroy(cfg_t *cfg) {
    if (!cfg) return;
    if (cfg->log_file && cfg->log_file != DEFAULT_LOG_STREAM) fclose(cfg->log_file);
    free(cfg->log_file_name);
    free(cfg);
}

cfg_t *config_from_file(char const *filename) {
    json_object *obj_cfg = json_object_from_file(filename), *obj;

    cfg_t *cfg = config_defaults();

    if (json_object_object_get_ex(obj_cfg, "admin_api_key", &obj)) {
        const char *admin_api_key_repr;
        if (!json_object_get_string_strict(obj, &admin_api_key_repr, NULL)) {
            putln_error_json_type(json_type_string, json_object_get_type(obj), "config: admin_api_key");
        }
        if (!uuid4_from_repr(&cfg->admin_api_key, json_object_get_string(obj))) {
            put_error("config: admin_api_key: invalid UUIDV4: %s", json_object_get_string(obj));
        }
    }
    if (json_object_object_get_ex(obj_cfg, "log_file", &obj)) {
        const char *log_file_name;
        int log_file_name_len;
        if (!json_object_get_string_strict(obj, &log_file_name, &log_file_name_len)) {
            putln_error_json_type(json_type_string, json_object_get_type(obj), "config: log_file");
        }
        FILE *log_file = fopen(log_file_name, "a");
        if (log_file) {
            cfg->log_file = log_file;
            if (!(cfg->log_file_name = strndup(log_file_name, log_file_name_len))) errno_exit("strndup");
        } else {
            put_error("config: could not open log file: %s\n", strerror(errno));
        }
    }
    if (json_object_object_get_ex(obj_cfg, "backlog", &obj) && !json_object_get_int_strict(obj, &cfg->backlog)) {
        putln_error_json_type(json_type_int, json_object_get_type(obj), "config: backlog");
    }
    if (json_object_object_get_ex(obj_cfg, "block_for", &obj) && !json_object_get_int_strict(obj, &cfg->block_for)) {
        putln_error_json_type(json_type_int, json_object_get_type(obj), "config: block_for");
    }
    if (json_object_object_get_ex(obj_cfg, "max_msg_length", &obj) && !json_object_get_int_strict(obj, &cfg->max_msg_length)) {
        putln_error_json_type(json_type_int, json_object_get_type(obj), "config: max_msg_length");
    }
    if (json_object_object_get_ex(obj_cfg, "page_inbox", &obj) && !json_object_get_int_strict(obj, &cfg->page_inbox)) {
        putln_error_json_type(json_type_int, json_object_get_type(obj), "config: page_inbox");
    }
    if (json_object_object_get_ex(obj_cfg, "page_outbox", &obj) && !json_object_get_int_strict(obj, &cfg->page_outbox)) {
        putln_error_json_type(json_type_int, json_object_get_type(obj), "config: page_outbox");
    }
    if (json_object_object_get_ex(obj_cfg, "port", &obj) && !json_object_get_uint16_strict(obj, &cfg->port)) {
        putln_error_json_type(json_type_int, json_object_get_type(obj), "config: port");
    }
    if (json_object_object_get_ex(obj_cfg, "rate_limit_h", &obj) && !json_object_get_int_strict(obj, &cfg->rate_limit_h)) {
        putln_error_json_type(json_type_int, json_object_get_type(obj), "config: rate_limit_h");
    }
    if (json_object_object_get_ex(obj_cfg, "rate_limit_m", &obj) && !json_object_get_int_strict(obj, &cfg->rate_limit_m)) {
        putln_error_json_type(json_type_int, json_object_get_type(obj), "config: rate_limit_m");
    }

    json_object_put(obj_cfg);

    return cfg;
}

void config_dump(cfg_t const *cfg) {
    puts("CONFIGURATION");
    printf("admin_api_key   ");
    uuid4_put(cfg->admin_api_key, stdout);
    putchar('\n');
    printf("backlog         %d\n", cfg->backlog);
    printf("block_for       %d seconds\n", cfg->block_for);
    printf("log_file        %s\n", coalesce(cfg->log_file_name, STR(DEFAULT_LOG_STREAM)));
    printf("max_msg_length  %d characters\n", cfg->max_msg_length);
    printf("page_inbox      %d\n", cfg->page_inbox);
    printf("page_outbox     %d\n", cfg->page_outbox);
    printf("port            %hd\n", cfg->port);
    printf("rate_limit_h    %d\n", cfg->rate_limit_h);
    printf("rate_limit_m    %d\n", cfg->rate_limit_m);
}

serial_t config_verify_api_key(config_verify_api_key_t *out_result, cfg_t const *cfg, api_key_t api_key, db_t *db) {
    if (uuid4_eq(api_key, cfg->admin_api_key)) {
        out_result->user_role = role_admin;
        out_result->user_id = 0;
        return errstatus_ok;
    }
    db_verify_user_api_key_t db_result;
    errstatus_t err = db_verify_user_api_key(&db_result, db, api_key);
    if (err == errstatus_ok) {
        out_result->user_id = db_result.user_id;
        switch (db_result.user_kind) {
        case user_kind_membre: out_result->user_role = role_membre; break;
        case user_kind_pro_prive: [[fallthrough]];
        case user_kind_pro_public: out_result->user_role = role_pro; break;
        }
    }
    return err;
}

int config_max_msg_length(cfg_t const *cfg) {
    return cfg->max_msg_length;
}
