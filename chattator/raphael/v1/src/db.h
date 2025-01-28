/// @file
/// @author Raphaël
/// @brief DAL - Interface
/// @date 23/01/2025

#ifndef DB_H
#define DB_H

#include "types.h"

/// @brief An opaque handle to a database connection.
typedef void db_t;

/// @brief Initialize a database connection.
/// @param verbosity The verbosity level.
/// @return A new database connection.
db_t *db_connect(int verbosity);

/// @brief Destroy a database connection.
/// @param db The database connection to destroy. No-op if @c NULL.
void db_destroy(db_t *db);

/// @brief Result of @ref db_verify_user_api_key.
typedef struct {
    /// @brief The kind of the user.
    user_kind_t user_kind;
    /// @brief The ID of the user.
    serial_t user_id;
} db_verify_user_api_key_t;

/// @brief Verify an API key.
/// @param out_result Assigned to the result.
/// @param db The database connection.
/// @param api_key The API key to verify.
/// @return The ID of the user who own this API key.
/// @return The error status. If an error occured, the value of @p out_result is untouched.
errstatus_t db_verify_user_api_key(db_verify_user_api_key_t *out_result, db_t *db, api_key_t api_key);

/// @brief Get the ID of an user from their e-mail.
/// @param db The database connection.
/// @param email The e-mail to look for.
/// @return The ID of the user with the specified e-mail.
/// @return @ref errstatus_t in case of failure.
serial_t db_get_user_id_by_email(db_t *db, char const *email);

/// @brief Get the ID of an user from their pseudo.
/// @param db The database connection.
/// @param pseudo The pseudo to look for.
/// @return The ID of the user with the specified pseudo.
/// @return @ref errstatus_t in case of failure.
serial_t db_get_user_id_by_pseudo(db_t *db, char const *pseudo);

/// @brief Fills a user record from its ID. If @p user->user_id is undefined, the behavior is undefined.
/// @param db The database connection.
/// @param user The user record to fill.
/// @return The error status of the operation.
errstatus_t db_get_user(db_t *db, user_t *user);

#endif // DB_H
