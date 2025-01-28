#ifndef ERRSTATUS_H
#define ERRSTATUS_H

#define put_error(fmt, ...) fprintf(stderr, PROG ": error: " fmt __VA_OPT__(, ) __VA_ARGS__)
#define put_error_json(fmt, ...) put_error("json-c: " fmt ": %s" __VA_ARGS__, json_util_get_last_err())

/// @brief An error status.
/// @remark errstatus constants are used in other integral expressions, by considering -1 and 0 as errors and any other value as successful. When this is the case, it will be documented.
typedef enum {
    min_errstatus = -1,
    /// @brief An error occured but it has already been handled, no action needed besides propagation.
    errstatus_handled = min_errstatus,
    /// @brief An error occurred.
    errstatus_error,
    /// @brief No error. Can be shadowed by the payload value in supertypes.
    errstatus_ok,
    /// @brief Largest value in the @ref errstatus_t enumeration.
    max_errstatus = errstatus_ok
} errstatus_t;

#endif // ERRSTATUS_H
