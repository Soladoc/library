#include "tchattator413.h"
#include "json-helpers.h"
#include <assert.h>

static inline json_object *act(json_object *const obj_action, cfg_t *cfg, db_t *db, server_t *server, fn_on_action_t on_action, fn_on_response_t on_response, void *on_ctx) {
    action_t action;

    if (!action_parse(&action, obj_action, db)) return NULL;
    if (on_action) on_action(&action, on_ctx);

    response_t response;
    if (!action_evaluate(&action, &response, cfg, db, server)) return NULL; 
    if (on_response) on_response(&response, on_ctx);

    json_object *obj_response = response_to_json(&response);

    return obj_response;
}

json_object *tchattator413_interpret(json_object *input, cfg_t *cfg, db_t *db, server_t *server, fn_on_action_t on_action, fn_on_response_t on_response, void *on_ctx) {
    json_object *output;

    json_type const input_type = json_object_get_type(input);
    json_object *item;
    switch (input_type) {
    case json_type_array: {
        int const len = json_object_array_length(input);
        output = json_object_new_array_ext(len);
        for (int i = 0; i < len; ++i) {
            json_object *const action = json_object_array_get_idx(input, i);
            assert(action);
            if ((item = act(action, cfg, db, server, on_action, on_response, on_ctx))) json_object_array_add(output, item);
        }
        break;
    }
    case json_type_object:
        output = json_object_new_array_ext(1);
        if ((item = act(input, cfg, db, server, on_action, on_response, on_ctx))) json_object_array_add(output, item);
        break;
    default:
        output = json_object_new_array_ext(0);
        putln_error_json_type_union2(json_type_array, json_type_object, input_type, "invalid request");
    }

    return output;
}
