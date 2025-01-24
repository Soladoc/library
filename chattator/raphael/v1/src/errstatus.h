#ifndef ERRSTATUS_H
#define ERRSTATUS_H

/// @brief An error status.
/// @remark errstatus constants are used in other integral expressions, by considering -1 and 0 as errors and any other value as successful. When this is the case, it will be documented.
typedef enum {
    /// @brief An error occured but it has already been handled, no action needed besides propagation.
    errstatus_handled = -1,
    /// @brief An error occurred.
    errstatus_error,
    /// @brief No error.
    errstatus_ok,
} errstatus_t;

#endif // ERRSTATUS_H
