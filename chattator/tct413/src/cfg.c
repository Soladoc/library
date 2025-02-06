/// @file
/// @author Raphaël
/// @brief Tchatator413 server configuration - Implementation
/// @date 29/01/2025

#include <errno.h>
#include <json-c/json.h>
#include <stdlib.h>
#include <string.h>
#include <tchatator413/cfg.h>
#include <tchatator413/json-helpers.h>
#include <tchatator413/util.h>
#include <time.h>

#define DEFAULT_LOG_STREAM stderr

struct cfg {
    uuid4_t admin_api_key;
    FILE *log_file;
    size_t max_msg_length;
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

static void _vlog(char const *file, int line, FILE *stream, log_lvl_t lvl, char const *fmt, va_list ap) {
    time_t t = time(NULL);
    fprintf(stream, "%s: ", ctime(&t));
    switch (lvl) {
    case log_error: fprintf(stream, "error"); break;
    case log_info: fprintf(stream, "info"); break;
    case log_warning: fprintf(stream, "warning"); break;
    }
    fprintf(stream, ": %s:%d: ", file, line);
    vfprintf(stream, fmt, ap);
}

static void _log(char const *file, int line, FILE *stream, log_lvl_t lvl, char const *fmt, ...) {
    va_list ap;
    va_start(ap, fmt);
    _vlog(file, line, stream, lvl, fmt, ap);
    va_end(ap);
}

#define log(stream, lvl, fmt, ...) _log(__FILE_NAME__, __LINE__, stream, lvl, fmt __VA_OPT__(, ) __VA_ARGS__)

cfg_t *cfg_defaults(void) {
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

void cfg_destroy(cfg_t *cfg) {
    if (!cfg) return;
    if (cfg->log_file && cfg->log_file != DEFAULT_LOG_STREAM) fclose(cfg->log_file);
    free(cfg->log_file_name);
    free(cfg);
}

#define INTRO "config: "

cfg_t *cfg_from_file(char const *filename) {
    json_object *obj_cfg = json_object_from_file(filename), *obj;

    cfg_t *cfg = cfg_defaults();

    if (!obj_cfg) {
        log(stderr, log_error, INTRO log_fmt_json_c("failed to parse config file at '%s'"));
        log(stderr, log_info, INTRO "using defaults\n");
        return cfg;
    }

    if (json_object_object_get_ex(obj_cfg, "admin_api_key", &obj)) {
        slice_t admin_api_key_repr;
        if (!json_object_get_string_strict(obj, &admin_api_key_repr)) {
            log(stderr, log_error, INTRO log_fmt_json_type(json_type_string, json_object_get_type(obj), "admin_api_key"));
        }
        if (!uuid4_parse_slice(&cfg->admin_api_key, admin_api_key_repr)) {
            log(stderr, log_error, INTRO "admin_api_key: invalid UUIDV4: %*s\n", slice_leni(admin_api_key_repr), admin_api_key_repr.val);
        }
    }
    if (json_object_object_get_ex(obj_cfg, "log_file", &obj)) {
        slice_t log_file_name;
        if (!json_object_get_string_strict(obj, &log_file_name)) {
            log(stderr, log_error, INTRO log_fmt_json_type(json_type_string, json_object_get_type(obj), "log_file"));
        }
        FILE *log_file = fopen(log_file_name.val, "a");
        if (log_file) {
            cfg->log_file = log_file;
            if (!(cfg->log_file_name = strndup(log_file_name.val, log_file_name.len))) errno_exit("strndup");
        } else {
            log(stderr, log_error, INTRO "could not open log file: %s\n", strerror(errno));
        }
    }
    if (json_object_object_get_ex(obj_cfg, "backlog", &obj) && !json_object_get_int_strict(obj, &cfg->backlog)) {
        log(stderr, log_error, INTRO log_fmt_json_type(json_type_int, json_object_get_type(obj), "backlog"));
    }
    if (json_object_object_get_ex(obj_cfg, "block_for", &obj) && !json_object_get_int_strict(obj, &cfg->block_for)) {
        log(stderr, log_error, INTRO log_fmt_json_type(json_type_int, json_object_get_type(obj), "block_for"));
    }
    if (json_object_object_get_ex(obj_cfg, "max_msg_length", &obj)) {
        int64_t max_msg_length;
        if (!json_object_get_int64_strict(obj, &max_msg_length)) {
            log(stderr, log_error, INTRO log_fmt_json_type(json_type_int, json_object_get_type(obj), "max_msg_length"));
        }
        if (max_msg_length < 0) {
            log(stderr, log_error, INTRO "max_msg_length: must be > 0\n");
        } else {
            cfg->max_msg_length = (size_t)max_msg_length;
        }
    }
    if (json_object_object_get_ex(obj_cfg, "page_inbox", &obj) && !json_object_get_int_strict(obj, &cfg->page_inbox)) {
        log(stderr, log_error, INTRO log_fmt_json_type(json_type_int, json_object_get_type(obj), "page_inbox"));
    }
    if (json_object_object_get_ex(obj_cfg, "page_outbox", &obj) && !json_object_get_int_strict(obj, &cfg->page_outbox)) {
        log(stderr, log_error, INTRO log_fmt_json_type(json_type_int, json_object_get_type(obj), "page_outbox"));
    }
    if (json_object_object_get_ex(obj_cfg, "port", &obj) && !json_object_get_uint16_strict(obj, &cfg->port)) {
        log(stderr, log_error, INTRO log_fmt_json_type(json_type_int, json_object_get_type(obj), "port"));
    }
    if (json_object_object_get_ex(obj_cfg, "rate_limit_h", &obj) && !json_object_get_int_strict(obj, &cfg->rate_limit_h)) {
        log(stderr, log_error, INTRO log_fmt_json_type(json_type_int, json_object_get_type(obj), "rate_limit_h"));
    }
    if (json_object_object_get_ex(obj_cfg, "rate_limit_m", &obj) && !json_object_get_int_strict(obj, &cfg->rate_limit_m)) {
        log(stderr, log_error, INTRO log_fmt_json_type(json_type_int, json_object_get_type(obj), "rate_limit_m"));
    }

    json_object_put(obj_cfg);

    return cfg;
}

void cfg_dump(cfg_t const *cfg) {
    puts("CONFIGURATION");
    printf("admin_api_key   ");
    uuid4_put(cfg->admin_api_key, stdout);
    putchar('\n');
    printf("backlog         %d\n", cfg->backlog);
    printf("block_for       %d seconds\n", cfg->block_for);
    printf("log_file        %s\n", COALESCE(cfg->log_file_name, STR(DEFAULT_LOG_STREAM)));
    printf("max_msg_length  %zu characters\n", cfg->max_msg_length);
    printf("page_inbox      %d\n", cfg->page_inbox);
    printf("page_outbox     %d\n", cfg->page_outbox);
    printf("port            %hd\n", cfg->port);
    printf("rate_limit_h    %d\n", cfg->rate_limit_h);
    printf("rate_limit_m    %d\n", cfg->rate_limit_m);
}

void _cfg_log(char const *file, int line, cfg_t const *cfg, log_lvl_t lvl, char const *fmt, ...) {
    va_list ap;
    va_start(ap, fmt);
    _vlog(file, line, cfg->log_file, lvl, fmt, ap);
    va_end(ap);
}

void cfg_log_endl(cfg_t const *cfg) {
    putc('\n', cfg->log_file);
}

#define DEFINE_CONFIG_GETTER(type, attr) \
    type cfg_##attr(cfg_t const *cfg) {  \
        return cfg->attr;                \
    }

DEFINE_CONFIG_GETTER(uuid4_t, admin_api_key)
DEFINE_CONFIG_GETTER(size_t, max_msg_length)
DEFINE_CONFIG_GETTER(int, page_inbox)
DEFINE_CONFIG_GETTER(int, page_outbox)
DEFINE_CONFIG_GETTER(int, rate_limit_m)
DEFINE_CONFIG_GETTER(int, rate_limit_h)
DEFINE_CONFIG_GETTER(int, block_for)
DEFINE_CONFIG_GETTER(int, backlog)
DEFINE_CONFIG_GETTER(uint16_t, port)
