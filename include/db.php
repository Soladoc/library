<?php
namespace DB;
use PDO, Throwable;
require_once 'util.php';

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

$_pdo = null;

/**
 * Se connecter à la base de données.
 *
 * La valeur retournée par cette fonction est cachée : l'appeler plusieurs fois n'a aucun effet. Il n'y a donc pas besoin de conserber son résultat dans une variable.
 * @return PDO L'objet PDO connecté à la base de données.
 */
function connect(): PDO
{
    global $_pdo;
    if ($_pdo !== null) {
        return $_pdo;
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
    // Pour le dév. en localhost: on n'a pas accès au conteneur postgresdb, on utilise donc le FQDN.
    $host = _is_localhost() ? '413.ventsdouest.dev' : 'postgresdb';
    $port = notfalse(getenv('PGDB_PORT'), 'PGDB_PORT not set');
    $dbname = 'postgres';

    $_pdo = new PDO(
        "$driver:host=$host;port=$port;dbname=$dbname",
        notfalse(getenv('DB_USER'), 'DB_USER not set'),
        notfalse(getenv('DB_ROOT_PASSWORD'), 'DB_ROOT_PASSWORD not set'),
    );

    notfalse($_pdo->exec("set schema 'pact'"));

    $_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $_pdo;
}

/**
 * Effectue une transaction.
 *
 * Cette fonction automatise BEGIN, COMMIT et ROLLBACK pour effectuer une transaction dans la base de données.
 *
 * Regrouper les statements liés dans une transaction permet notamment de préserver la cohérence de la base de données en cas d'erreur.
 *
 * @param callable $body La fonction contenant le corps de la transaction. Elle est appelée entre le BEGIN et le COMMIT. Si cette fonction jette une exception, un ROLLBACK est effectué.
 * @param ?callable $cleanup La fonction à appeler pour effectuer un nettoyage additionnel lorsque $body jette une exception, avant le ROLLBACK. Optionnel.
 */
function transaction(callable $body, ?callable $cleanup = null)
{
    $pdo = connect();
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
