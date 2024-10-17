<?php

/**
 * Asserts that something is not false.
 * @template T
 * @param T|false $value Value, possibly false.
 * @param string $msg Assertion message.
 * @return T Result, not false.
 */
function notfalse(mixed $value, string $msg): mixed
{
    if ($value === false) {
?><pre><?= $msg ?></pre><?php
        exit(1);
    }
    return $value;
}

function db_connect(): PDO
{
    return new PDO(
        'psgsql:host=postgresdb;port=' . notfalse(getenv('PGDB_PORT'), 'PGDB_PORT not set') . ';dbname=postgres',
        notfalse(getenv('DB_USER'), 'DB_USER not set'),
        notfalse(getenv('DB_ROOT_PASSWORD'), 'DB_ROOT_PASSWORD not set'),
    );
}
