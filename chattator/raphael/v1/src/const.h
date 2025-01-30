/// @file
/// @author Raphaël
/// @brief General constants - Standalone header
/// @date 23/01/2025

#ifndef CONST_H
#define CONST_H

// https://en.wikipedia.org/wiki/X_macro

#define ADMIN_PASSWORD_HASH "$2y$10$YiDc/A/8DR9YSVohn7Dh9u5rb7DaiKvG/2iMRF3Xo8byNkOPEY0Sq"

#define X_ACTIONS(X) \
    X(login)         \
    X(logout)        \
    X(whois)         \
    X(send)          \
    X(motd)          \
    X(inbox)         \
    X(outbox)        \
    X(edit)          \
    X(rm)            \
    X(block)         \
    X(unblock)       \
    X(ban)           \
    X(unban)

#endif // CONST_H
