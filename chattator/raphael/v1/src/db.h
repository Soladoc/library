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

/// @brief Verify an API key.
/// @param db The database connection.
/// @param api_key The API key to verify.
/// @return @c true when the API key is valid
/// @return @c false otherwise
bool db_verify_api_key(db_t *db, api_key_t api_key);

/// @brief Get the ID of an user from their e-mail.
/// @param db The database connection.
/// @param email The e-mail to look for.
/// @return The ID of the user with the specified e-mail.
/// @return @c 0 if an error occured or there is no user of such email.
serial_t db_get_user_id_by_email(db_t *db, const char *email);

/// @brief Get the ID of an user from their pseudo.
/// @param db The database connection.
/// @param pseudo The pseudo to look for.
/// @return The ID of the user with the specified pseudo.
/// @return @c 0 if an error occured or their is no user of sech pseudo.
serial_t db_get_user_id_by_pseudo(db_t *db, const char *pseudo);

/// @brief Fills a user record from its ID. If @p user->user_id is undefined, the behavior is undefined.
/// @param db The database connection.
/// @param user The user record to fill.
/// @return @p true on success
/// @return @p false one failure.
bool db_get_user(db_t *db, user_t *user);

#endif // DB_H
