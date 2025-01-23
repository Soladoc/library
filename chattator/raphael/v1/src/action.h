#ifndef ACTION_H
#define ACTION_H

#include <json-c/json.h>
#include <stdbool.h>
#include <stdio.h>

#include "types.h"
#include "db.h"

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
    };
};

/// @brief Parse an action from a JSON object.
/// @param action Mutated to the parsed action.
/// @param obj The JSON object allegedly containing an action.
/// @param db The DB connection to query the database for supplemental information.
/// @return `true` on success, `false` on failure.
bool action_parse(struct action *action, json_object *obj, struct db_connection *db);

/// @brief Destroys an action.
/// @param action The action to destroy. No-op if NULL.
void action_destroy(struct action const *action);

#ifndef NDEBUG
void action_explain(struct action const *action, FILE *output);
#endif // NDEBUG

#endif // ACTION_H
