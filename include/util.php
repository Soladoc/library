<?php

/**
 * Checks if all elements in the given array satisfy the provided predicate.
 * Determines whether a predicate matches every element in an array.
 * @template T
 * @param T[] $arr The array to check.
 * @param callable(T): bool $predicatee The predicate function to apply to each element.
 * @return bool True if all elements satisfy the predicate, false otherwise.
 */
function array_every(array $arr, callable $predicate): bool {
    foreach ($arr as $e) {
        if (!$predicate($e)) {
            return false;
        }
    }
    return true;
}

/**
 * Retrieves an argument from the given source array, with optional validation and transformation.
 * @param array $source The source array to retrieve the argument from.
 * @param string $name The name of the argument to retrieve.
 * @param bool $required Whether the argument is required. If true and the argument is not present, an error will be thrown.
 * @param ?callable(string, mixed): mixed $filter An optional filter to apply to the value, given by `arg_check` or `arg_filter`. An error message is shown and the script exists if the filter doesn't match.
 * @return mixed The retrieved and (optionally) transformed argument value. If not required and missing, `null` is returned.
 */
function getarg(array $source, string $name, ?callable $filter = null, bool $required = true): mixed
{
    if (!isset($source[$name])) {
        if ($required) {
            html_error("argument manquant: $name");
        } else {
            return null;
        }
    }
    return $filter === null ? $source[$name]: $filter($name, $source[$name]);
}

/**
 * Returns a function that applies the given validation check to an argument value.
 * @param callable $check An validation check function that takes the argument value and returns a boolean indicating if the value is valid.
 * @return callable(string, mixed): mixed A function that takes an argument name and value, applies the validation check, and returns the value if it is valid, or throws an error if it is not.
 */
function arg_check(callable $check): callable
{
    return function (string $name, mixed $value) use ($check) {
        if (!$check($value)) {
            html_error("argument $name invalide: " . var_export($value, true));
        }
        return $value;
    };
}

/**
 * Returns a function that applies the given filter and options to an argument value.
 * @param int $filter The filter to apply to the argument value.
 * @param array|int $options The options to use with the filter.
 * @return callable(string, mixed): mixed A function that takes an argument name and value, applies the filter and options, and returns the transformed value.
 */
function arg_filter(int $filter, array|int $options = 0): callable
{
    return function (string $name, mixed $value) use ($filter, $options) {
        $result = filter_var($value, $filter, $options);
        if ($result === false) {
            html_error("argument $name invalide: " . var_export($value, true));
        }
        return $result;
    };
}

/**
 * Outputs an HTML error message and throws an exception.
 * @param mixed $arg The value to include in the error message. Is thrown if it is an instance of `Throwable`, otherwise, `Exception` is thrown.
 */
function html_error(mixed $arg): never
{
    ?>
    <p>Erreur: <?= strval($arg) ?></p><?php
    if ($arg instanceof Throwable) {
        throw $arg;
    }
    throw new Exception($arg);
}

/**
 * Returns a string with the given value and suffix, or an empty string if the value is falsey.
 * @param ?string $value The value to format.
 * @param ?string $suffix The suffix to append to the value.
 * @return string The formatted string.
 */
function elvis(?string $value, ?string $suffix): string
{
    return $value ? "$value$suffix" : '';
}

/**
 * Returns a function that checks if a value is contained in a list of allowed values.
 * @param string[] $allowed_values The allowed values.
 * @return callable(string): bool
 */
function f_is_in(array $allowed_values): callable
{
    return fn($value) => array_search($value, $allowed_values) !== false;
}

/**
 * Returns a function that checks if an array contains all the specified keys.
 * @param array $keys The keys to check for.
 * @return callable(array): bool A function that takes an array and returns true if it contains all the specified keys.
 */
function f_array_has_keys(array $keys): callable
{
    return fn($value) => is_array($value) && array_every($keys, fn($key) => isset($value[$key]));
}

/**
 * Returns the single value in the given array.
 * @template T
 * @param array<T> $array The array to extract the single value from.
 * @return T The single value in the array.
 * @throws Exception If the array does not contain exactly one value.
 */
function single(array $array): mixed
{
    if (count($array) !== 1) {
        throw new Exception('Array contains not a single value');
    }
    return $array[0];
}

/**
 * Returns the single value in the given array.
 * @template T
 * @param array<T> $array The array to extract the single value from.
 * @param T $default The default value to return if the array is empty.
 * @return T The single value in the array.
 * @throws Exception If the array contains more than one value.
 */
function single_or_default(array $array, mixed $default = null): mixed
{
    if (empty($array)) {
        return $default;
    }
    if (count($array) !== 1) {
        throw new Exception('Array contains not a single value');
    }
    return $array[0];
}

/**
 * Converts a "structre of arrays" (SOA) to an "array of structures" (AOS).
 *
 * The input `$array` is expected to be an array where each element is an associative
 * array with the same set of keys. This function will return a new array where each
 * element is an array containing the values for each key from the input array.
 * @param array<array> $array The input "structre of arrays" to convert.
 * @return array<int, mixed> The resulting "array of structures".
 * 
 * @example
 * Input: ['a' => [1, 2], 'b' => [3, 4]]
 * Output: [[a' => 1, 'b' => 3], ['a' => 2, 'b' => 4]]
 */
function soa_to_aos(array $array): array
{
    $result = [];
    foreach ($array as $key => $all) {
        foreach ($all as $i => $val) {
            $result[$i][$key] = $val;
        }
    }
    return $result;
}
