<?php

/**
 * Return values at keys in $array, optionally performing validation. If one of the keys it missing, outputs an HTML error message and exits.
 * @param array $array The array to test.
 * @param array<string|array{string, callable(string): bool}> $keys The keys $array must contain.
 * @return array The extracted keys.
 */
function get_args(array $array, array $keys)
{
    $result = [];
    foreach ($keys as $item) {
        [$key, $check] = is_array($item) ? $item : [$item, $check = fn($_) => true];

        if (!isset($array[$key])) {
            html_error("argument manquant: $key");
        }
        if (!$check($array[$key])) {
            html_error("argument invalide: $key");
        }

        $result[] = $array[$key];
    }

    return $result;
}

function html_error(string $msg): never
{
    ?>
    <p>Erreur: <?= $msg ?></p><?php
    exit;
}

function elvis(?string $value, ?string $suffix): string
{
    return $value ? "$value$suffix" : '';
}
/**
 * Returns a function that checks if a string is contained in a list of allowed values.
 * @param string[] $allowed_values The allowed values.
 * @return callable(string): bool
 */
function f_str_is(string ...$allowed_values): callable
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
