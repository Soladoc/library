<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

/**
 * Asserts that something is not false.
 * @template T
 * @param T|false $value Value, possibly false.
 * @param string $msg Assertion message.
 * @return T Result, not false.
 */
function notfalse(mixed $value, string $msg = 'was false'): mixed
{
    if ($value === false) {
        ?>
        <pre><?= $msg ?></pre>
        <?php
        throw new Exception($msg);
    }
    return $value;
}

function _is_localhost(): bool
{
    $server_ip = null;

    if (defined('INPUT_SERVER') && filter_has_var(INPUT_SERVER, 'REMOTE_ADDR')) {
        $server_ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
    } elseif (defined('INPUT_ENV') && filter_has_var(INPUT_ENV, 'REMOTE_ADDR')) {
        $server_ip = filter_input(INPUT_ENV, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $server_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    }

    if (empty($server_ip)) {
        $server_ip = '127.0.0.1';
    }

    return empty(filter_var($server_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE));
}

$_PDO = null;

/**
 * Connect to the database.
 *
 * The return value of this function is cached.
 *
 * @return PDO The database PDO object.
 */
function db_connect(): PDO
{
    global $_PDO;
    if ($_PDO !== null) {
        return $_PDO;
    }

    // Load .env file
    {
        $envfile = __DIR__ . '/.env';
        foreach (notfalse(file($envfile, FILE_SKIP_EMPTY_LINES), "dotenv file missing at $envfile") as $line) {
            notfalse(putenv(trim($line)));
        }
    }

    // Connect to the database
    $driver = 'pgsql';
    // Pour le dév. en localhost: on a accès au conteneur postgresdb, on utilise donc le FQDN.
    $host = _is_localhost() ? '413.ventsdouest.dev' : 'postgresdb';
    /** @var int */
    $port = notfalse(getenv('PGDB_PORT'), 'PGDB_PORT not set');
    $dbname = 'postgres';

    $_PDO = new PDO(
        "$driver:host=$host;port=$port;dbname=$dbname",
        notfalse(getenv('DB_USER'), 'DB_USER not set'),
        notfalse(getenv('DB_ROOT_PASSWORD'), 'DB_ROOT_PASSWORD not set'),
    );

    notfalse($_PDO->exec("set schema 'pact'"));

    $_PDO->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $_PDO;
}

/**
 * Call a function inside a transaction.
 *
 * This function automates the beginning, commit and rollback of a transaction.
 *
 * @param callable $body The body of the transaction. Any @see \Throwable thrown from this function will result in a rollback.
 * @param ?callable $cleanup Additional cleanup logic to run when an exception is catched from @p $body, before rolling back.
 */
function transaction(callable $body, ?callable $cleanup = null)
{
    $pdo = db_connect();
    notfalse($pdo->beginTransaction(), '$pdo->beginTransaction() failed');

    try {
        $body();
        $pdo->commit();
    } catch (Throwable $e) {
        if ($cleanup !== null)
            $cleanup();
        notfalse($pdo->rollBack(), '$pdo->rollBack() failed');
        throw $e;
    }
}
