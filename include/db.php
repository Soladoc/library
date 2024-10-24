<?php

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
?><pre><?= $msg ?></pre><?php
        throw new Exception($msg);
    }
    return $value;
}

function load_dotenv()
{
    $envfile = __DIR__ . '/.env';
    $env = notfalse(file_get_contents($envfile), "dotenv file missing at $envfile");
    foreach (explode("\n", $env) as $line) {
        preg_match('/([^#]+)\=(.*)/', $line, $matches);
        if (isset($matches[2])) {
            putenv(trim($line));
        }
    }
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

function db_connect(): PDO
{
    global $_PDO;
    if ($_PDO !== null) {
        return $_PDO;
    }
    try {
        load_dotenv();

        $driver = 'pgsql';
        // Pour le dév. en localhost: on a accès au conteneur postgresdb, on utilise donc le FQDN.
        $host = _is_localhost() ? '413.ventsdouest.dev' : 'postgresdb';
        $port = notfalse(getenv('PGDB_PORT'), 'PGDB_PORT not set');
        $dbname = 'postgres';

        return $_PDO = new PDO(
            "$driver:host=$host;port=$port;dbname=$dbname",
            notfalse(getenv('DB_USER'), 'DB_USER not set'),
            notfalse(getenv('DB_ROOT_PASSWORD'), 'DB_ROOT_PASSWORD not set'),
        );
    } catch (Exception $e) {
        ?><pre><?php print_r($e) ?></pre><?php
        exit($e->getCode());
    }
}
