#include <assert.h>
#include <tchattator413/json-helpers.h>
#include <tchattator413/tchattator413.h>

static inline json_object *act(json_object *const obj_action, cfg_t *cfg, db_t *db, server_t *server, fn_on_action_t on_action, fn_on_response_t on_response, void *on_ctx) {
    action_t action = action_parse(obj_action, db);
    if (on_action) on_action(&action, on_ctx);

    response_t response = action_evaluate(&action, cfg, db, server);
    if (on_response) on_response(&response, on_ctx);

    json_object *obj_response = response_to_json(&response);

    return obj_response;
}

json_object *tchattator413_interpret(json_object *input, cfg_t *cfg, db_t *db, server_t *server, fn_on_action_t on_action, fn_on_response_t on_response, void *on_ctx) {
    json_object *output;

    json_type const input_type = json_object_get_type(input);
    switch (input_type) {
    case json_type_array: {
        size_t const len = json_object_array_length(input);
        output = json_object_new_array_ext(len);
        for (size_t i = 0; i < len; ++i) {
            json_object *const action = json_object_array_get_idx(input, i);
            assert(action);
            json_object_array_add(output, act(action, cfg, db, server, on_action, on_response, on_ctx));
        }
        assert(len == json_object_array_length(output)); // Same amount of input and output actions
        break;
    }
    case json_type_object:
        output = json_object_new_array_ext(1);
        json_object_array_add(output, act(input, cfg, db, server, on_action, on_response, on_ctx));
        break;
    default:
        output = json_object_new_array_ext(1);
        json_object_array_add(output,
            response_to_json(&(response_t) {
                .type = action_type_error,
                .body.error = {
                    .type = action_error_type_type,
                    .info.type = {
                        .expected = json_type_object,
                        .obj_actual = input,
                        .location = "request",
                    },
                },
            }));
    }

    return output;
}
