<?php

/**
 * Retrieves an argument from the given source array, with optional validation and transformation.
 * @param array $source The source array to retrieve the argument from.
 * @param string $name The name of the argument to retrieve.
 * @param bool $required Whether the argument is required. If true and the argument is not present, an error will be thrown.
 * @param ?callable(string, mixed): mixed $filter An optional filter to apply to the value, given by `arg_check` or `arg_filter`. An error message is shown and the script exists if the filter doesn't match.
 * @return mixed The retrieved and (optionally) transformed argument value.
 */
function getarg(array $source, string $name, ?callable $filter = null, bool $required = true): mixed
{
    if ($required && !isset($source[$name])) {
        html_error("argument manquant: $name");
    }
    return $filter === null ? $source[$name] : $filter($name, $source[$name]);
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
 * Returns a function that checks if a string is contained in a list of allowed values.
 * @param string[] $allowed_values The allowed values.
 * @return callable(string): bool
 */
function f_str_is(array $allowed_values): callable
{
    return fn($value) => array_search($value, $allowed_values) !== false;
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
 * Converts a "single object array" (SOA) to an "array of structures" (AOS).
 *
 * The input `$array` is expected to be an array where each element is an associative
 * array with the same set of keys. This function will return a new array where each
 * element is an array containing the values for each key from the input array.
 * @param array $array The input "single object array" to convert.
 * @return array The resulting "array of structures".
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
