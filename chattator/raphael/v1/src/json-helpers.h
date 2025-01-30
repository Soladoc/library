#ifndef JSON_HELPERS
#define JSON_HELPERS

#include "types.h"
#include "util.h"
#include <json-c/json_types.h>
#include <stdbool.h>
#include <stdint.h>

#define put_error_json_c(fmt, ...) put_error("json-c: " fmt ": %s" __VA_ARGS__, json_util_get_last_err());

#define putln_error_json_type_union2(type1, type2, actual, fmt, ...) put_error("json: " fmt ": type: expected %s or %s, got %s\n" __VA_OPT__(,) __VA_ARGS__, json_type_to_name(type1), json_type_to_name(type2), json_type_to_name(actual))

#define putln_error_json_type(type, actual, fmt, ...) put_error("json: " fmt ": type: expected %s, got %s\n" __VA_OPT__(,) __VA_ARGS__, json_type_to_name(type), json_type_to_name(actual))

#define putln_error_json_missing_key(key, fmt, ...) put_error("json: " fmt ": missing key: " key "\n" __VA_OPT__(,) __VA_ARGS__)

// JSON-C performs type coercion when getting values of the wrong type. That's confusing. We don't want that.
// Creating explicit "strict" variants of each necessary getter function.

/// @brief Get the 16-bit unsigned integer value of a JSON object.
/// @param obj The JSON object to get the value of.
/// @param out Assigned to the integer value of the object. Pass @c NULL to only check the type.
/// @return @c true when @p obj is of the integer type.
/// @return @c false otherwise. @p out is untouched.
/// @remark Values are clamped between @c 0 and @ref UINT16_MAX.
bool json_object_get_uint16_strict(json_object const *obj, uint16_t *out);

/// @brief Get the 32-bit integer value of a JSON object.
/// @param obj The JSON object to get the value of.
/// @param out Assigned to the integer value of the object. Pass @c NULL to only check the type.
/// @return @c true when @p obj is of the integer type.
/// @return @c false otherwise. @p out is untouched.
/// @remark Values are clamped between @ref INT32_MIN and @ref INT32_MAX.
bool json_object_get_int_strict(json_object const *obj, int32_t *out);

/// @brief Get the 64-bit integer value of a JSON object.
/// @param obj The JSON object to get the value of.
/// @param out Assigned to the integer value of the object. Pass @c NULL to only check the type.
/// @return @c true when @p obj is of the integer type.
/// @return @c false otherwise. @p out is untouched.
/// @remark Values are clamped between @ref INT64_MIN and @ref INT64_MAX.
bool json_object_get_int64_strict(json_object const *obj, int64_t *out);

/// @brief Get the string value of a JSON object.
/// @param obj The JSON object to get the value of.
/// @param out Assigned to the string value of the object. Pass @c NULL to only check the type.
/// @return @c true when @p obj is of the string type.
/// @return @c false otherwise.  @p out is untouched.
bool json_object_get_string_strict(json_object *obj, slice_t *out);

#endif // JSON_HELPERS