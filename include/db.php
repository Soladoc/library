<?php
namespace DB;

use PDO, PDOStatement;

require_once 'util.php';

const PDO_PARAM_DECIMAL = PDO::PARAM_STR;

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
    } catch (\Throwable $e) {
        if ($cleanup !== null)
            $cleanup();
        notfalse($pdo->rollBack(), '$pdo->rollBack() failed');
        throw $e;
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

// Query construction functions

enum BoolOperator: string
{
    case AND = 'and';
    case OR = 'or';
}

function quote_identifier(string $identifier): string
{
    return '"' . str_replace('"', '""', $identifier) . '"';
}

function quote_string(string $string): string
{
    return "'" . str_replace("'", "''", $string) . "'";
}

/**
 * Generates a WHERE clause for a SQL query based on an array of key-value pairs.
 *
 * @param BoolOperator $operator The logical operator to use between clauses.
 * @param array $clauses An array containing the conditions for the WHERE clause.
 * @return string The generated WHERE clause, or an empty string if no clauses are provided.
 */
function where_clause(BoolOperator $operator, array $clauses): string
{
    return $clauses
        ? ' where ' . implode(" $operator->value ", array_map(fn($attr) => "$attr = :$attr", $clauses))
        : '';
}

/**
 * Prépare un *statement* INSERT INTO pour 1 ligne retournant la colonne *id*.
 * @param string $table La table dans laquelle insérer
 * @param array $values Les noms de colonne => leur valeur.
 * @return PDOStatement Une *statement* prêt à l'exécution, retournant un table 1x1, la valeur de la colonne ID.
 */
function insert_into_returning_id(string $table, array $values): PDOStatement
{
    if (!$values) {
        return notfalse(connect()->prepare("insert into $table default values"));
    }
    $column_names = implode(',', array_keys($values));
    $arg_names = implode(',', array_map(fn($col) => ":$col", array_keys($values)));
    $stmt = notfalse(connect()->prepare("insert into $table ($column_names) values ($arg_names) returning id"));
    bind_values($stmt, $values);
    return $stmt;
}

/**
 * Prépare un *statement* UPDATE.
 * @param string $table La table dans la quelle mettre à jour.
 * @param array $values Les colonnes à modifier => leurs valeurs pour la clause SET du UPDATE.
 * @param array $where_key_values Les colonnes clés => leurs valeurs pour la clause WHERE du UPDATE.
 * @return PDOStatement Un *statement* prêt à l'exécution, ne retournant rien.
 */
function update(string $table, array $values, array $where_key_values): PDOStatement
{
    if (!$values) {
        return notfalse(connect()->prepare('select null'));  // todo: does a empty string work as a noop? test it when we get a working thing.
    }
    $sets = implode(',', array_map(fn($col) => "$col = :$col", array_keys($values)));
    $stmt = notfalse(connect()->prepare("update $table set $sets" . where_clause(BoolOperator::AND, $where_key_values)));
    bind_values($stmt, $values);
    return $stmt;
}

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
