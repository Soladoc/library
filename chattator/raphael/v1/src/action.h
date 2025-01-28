/// @file
/// @author Raphaël
/// @brief Tchattator413 protocol - Interface
/// @date 23/01/2025

#ifndef ACTION_H
#define ACTION_H

#include <json-c/json.h>
#include <stdbool.h>
#include <stdio.h>

#include "db.h"
#include "types.h"
#include "config.h"

/// @brief The type of an action.
enum action_type {
    action_type_login,
    action_type_logout,
    action_type_whois,
    action_type_send,
    action_type_motd,
    action_type_inbox,
    action_type_outbox,
    action_type_edit,
    action_type_rm,
    action_type_block,
    action_type_unblock,
    action_type_ban,
    action_type_unban,
};

/// @brief An action. Actions represent the commands the protocol implements.
struct action {
    enum action_type type;
    union {
        struct {
            api_key_t api_key;
            password_hash_t password_hash;
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
};

// Note : resemblance with HTTP status code is only for familiarity
enum status {
    status_ok = 200,
    status_created = 201,
    status_no_content = 204,
    status_unauthorized = 401,
    status_forbidden = 403,
    status_not_found = 404,
    status_payload_too_large = 413,
    status_unprocessable_content = 422,
    status_too_many_requests = 429,
    status_internal_serveur_error = 500,
};

struct response {
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
    enum status status;
    enum action_type type;
    bool has_next_page;
};

/// @brief Parse an action from a JSON object.
/// @param action Mutated to the parsed action.
/// @param obj The JSON object allegedly containing an action.
/// @param cfg The configuration.
/// @param db The DB connection.
/// @return The error status of the operation.
errstatus_t action_parse(struct action *action, json_object *obj, cfg_t *cfg, db_t *db);

/// @brief Destroys an action.
/// @param action The action to destroy. No-op if @c NULL.
void action_destroy(struct action const *action);

/// @brief Evaluate an action.
/// @param action The action to evaluate.
/// @param response Mutated to the response.
/// @param db The DB connection.
/// @return The error status of the operation.
errstatus_t action_evaluate(struct action const *action, struct response *response, db_t *db);

/// @brief Convert an action response to JSON.
/// @param response The action response.
/// @return A new JSON object.
json_object *response_to_json(struct response *response);

#ifndef NDEBUG
/// @brief Explain an action.
/// @param action The action to explain.
/// @param output The stream to write the exlanation to.
void action_explain(struct action const *action, FILE *output);
#endif // NDEBUG

#endif // ACTION_H
