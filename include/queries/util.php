<?php
namespace DB;

use PDO, PDOStatement;

const PDO_PARAM_DECIMAL = PDO::PARAM_STR;

/**
 * Binds types values to a statement.
 *
 * @param PDOStatement $stmt The statement on which to bind values.
 * @param array<int|string,array{mixed, int}> $params An associative array mapping from the parameter name to a tuple of the parameter value and the PDO type (e.g. a PDO::PARAM_* constant value)
 */
function bind_values(PDOStatement $stmt, array $params)
{
    foreach ($params as $name => [$value, $type]) {
        notfalse($stmt->bindValue($name, $value, $type));
    }
}

function filter_null_args(array $array): array
{
    return array_filter($array, fn($e) => $e[0] !== null);
}

/**
 * Generates a WHERE clause for a SQL query based on an array of key-value pairs.
 *
 * This function is an internal implementation detail and should not be called directly outside of this module, as it could pose a security risk.
 *
 * @param string $operator The logical operator to use between clauses (e.g. 'and', 'or').
 * @param array $clauses An array containing the conditions for the WHERE clause.
 * @return string The generated WHERE clause, or an empty string if no clauses are provided.
 */
function _where_clause(string $operator, array $clauses): string
{
    return $clauses
        ? ' where ' . implode(" $operator ", array_map(fn($attr) => "$attr = :$attr", $clauses))
        : '';
}

function _insert_into_returning_id(string $table, array $args): string
{
    assert(!empty($args));
    $column_names = implode(',', array_keys($args));
    $arg_names = implode(',', array_map(fn($attr) => ":$attr", array_keys($args)));
    return "insert into \"$table\" ($column_names) values ($arg_names) returning id";
}
