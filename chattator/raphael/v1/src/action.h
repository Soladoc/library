/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Interface
/// @date 23/01/2025

#ifndef ACTION_H
#define ACTION_H

#include <json-c/json_types.h>
#include <stdbool.h>
#include <stdio.h>

#include "config.h"
#include "db.h"
#include "server.h"
#include "types.h"

/// @brief The type of an action.
typedef enum {
#define X(name) action_type_##name,
    X_ACTIONS(X)
#undef X
} action_type_t;

#define action_type(name) CAT(action_type_, name)

/// @brief An action. Actions represent the commands the protocol implements.
typedef struct {
    action_type_t type;
    union {
        struct {
            api_key_t api_key;
            char *password;
        } login;
        struct {
            token_t token;
        } logout, motd;
        struct {
            api_key_t api_key;
            serial_t user_id;
        } whois;
        struct {
            token_t token;
            serial_t dest_user_id;
            char *content;
        } send;
        struct {
            token_t token;
            page_number_t page;
        } inbox, outbox;
        struct {
            token_t token;
            serial_t msg_id;
            char *new_content;
        } edit;
        struct {
            token_t token;
            serial_t msg_id;
        } rm;
        struct {
            token_t token;
            serial_t user_id;
        } block, unblock, ban, unban;
    } with;
} action_t;

// Note : resemblance with HTTP status code is only for familiarity
typedef enum {
    status_ok = 200,
    status_unauthorized = 401,
    status_forbidden = 403,
    status_not_found = 404,
    status_payload_too_large = 413,
    status_unprocessable_content = 422,
    status_too_many_requests = 429,
    status_internal_server_error = 500,
} status_t;

typedef struct {
    union {
        struct {
            token_t token;
        } login;
        user_t whois;
        struct {
            serial_t msg_id;
        } send;
        /*struct {

        } motd;
        struct {

        } inbox;
        struct {

        } outbox;
        struct {

        } edit;
        struct {

        } rm;
        struct {

        } block;
        struct {

        } unblock;
        struct {

        } ban;
        struct {

        } unban;*/
    } body;
    status_t status;
    action_type_t type;
    bool has_next_page;
} response_t;

/// @brief Put an user role.
/// @param role The role flags
/// @param stream The stream to write to.
void put_role(role_flags_t role, FILE *stream);

/// @brief Parse an action from a JSON object.
/// @param out_action Mutated to the parsed action.
/// @param obj The JSON object allegedly containing an action.
/// @param cfg The configuration.
/// @param db The DB connection.
/// @return @p true on success.
/// @return @p false on error.
bool action_parse(action_t *out_action, json_object *obj, cfg_t *cfg, db_t *db);

/// @brief Destroys an action.
/// @param action The action to destroy. No-op if @c NULL.
void action_destroy(action_t const *action);

/// @brief Evaluate an action.
/// @param action The action to evaluate.
/// @param response Mutated to the response.
/// @param cfg The configuration.
/// @param db The DB connection.
/// @return @p true on success.
/// @return @p false on error.
bool action_evaluate(action_t const *action, response_t *response, cfg_t *cfg, db_t *db, server_t *server);

/// @brief Convert an action response to JSON.
/// @param response The action response.
/// @return A new JSON object.
json_object *response_to_json(response_t *response);

#ifndef NDEBUG
/// @brief Explain an action.
/// @param action The action to explain.
/// @param output The stream to write the exlanation to.
void action_explain(action_t const *action, FILE *output);
#endif // NDEBUG

#endif // ACTION_H
