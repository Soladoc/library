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

$_dotenv_loaded = false;

function load_dotenv()
{
    global $_dotenv_loaded;
    
    if ($_dotenv_loaded) {
        return;
    }

    $env = notfalse(file_get_contents(__DIR__ . '/.env'), 'dotenv file missing');
    foreach (explode("\n", $env) as $line) {
        preg_match('/([^#]+)\=(.*)/', $line, $matches);
        if (isset($matches[2])) {
            putenv(trim($line));
        }
    }

    $_dotenv_loaded = true;
}

function db_connect(): PDO
{
    load_dotenv();
    return new PDO(
        'psgsql:host=postgresdb;port=' . notfalse(getenv('PGDB_PORT'), 'PGDB_PORT not set') . ';dbname=postgres',
        notfalse(getenv('DB_USER'), 'DB_USER not set'),
        notfalse(getenv('DB_ROOT_PASSWORD'), 'DB_ROOT_PASSWORD not set'),
    );
}
