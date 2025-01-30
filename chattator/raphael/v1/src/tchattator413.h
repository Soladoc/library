#ifndef TCHATTATOR_413_H
#define TCHATTATOR_413_H

#include "action.h"
#include "config.h"
#include "db.h"
#include "server.h"
#include <json-c/json.h>

typedef void (*fn_on_action_t)(const action_t *action, void *ctx);
typedef void (*fn_on_response_t)(const response_t *response, void *ctx);

json_object *tchattator413_interpret(json_object *input, cfg_t *cfg, db_t *db, server_t *server, fn_on_action_t on_action, fn_on_response_t on_response, void *on_ctx);


#endif // TCHATTATOR_413_H