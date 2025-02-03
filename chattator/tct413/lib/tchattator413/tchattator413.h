/// @file
/// @author Raphaël
/// @brief Facade interface
/// @date 1/02/2025

#ifndef TCHATTATOR_413_H
#define TCHATTATOR_413_H

#include "action.h"
#include "cfg.h"
#include "db.h"
#include "server.h"
#include <json-c.h>

typedef void (*fn_on_action_t)(const action_t *action, void *ctx);
typedef void (*fn_on_response_t)(const response_t *response, void *ctx);

json_object *tchattator413_interpret(json_object *input, cfg_t *cfg, db_t *db, server_t *server, fn_on_action_t on_action, fn_on_response_t on_response, void *on_ctx);

int tchattator413_run_console(cfg_t *cfg, db_t *db, server_t *server, int argc, char **argv);

int tchattator413_run_server(cfg_t *cfg, db_t *db, server_t *server);

#endif // TCHATTATOR_413_H
