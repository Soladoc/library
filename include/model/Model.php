<?php
require_once 'db.php';

abstract class Model
{
    /**
     * Table name.
     * @var string
     */
    const TABLE = null;  // abstract constant, chaque sous-classe doit le définir

    /**
     * Colonnes pouvant être insérées/mises à jour.
     * column name => [map from PHP attribute value to DB column or `null` for identity, attribute name, PDO param type]
     * @return array<string, array{?callable(mixed): mixed, string, int}>
     */
    protected static function fields(): array { return []; }

    /**
     * Colonnes clé identifiant de manière unique une ligne.
     * column name => [map from DB column to PHP attribute value or null for identity, attribute name, PDO param type]
     * @return array<string, array{?callable(mixed): mixed, string, int}>
     */
    protected static function key_fields(): array { return []; }

    /**
     * Colonnes supplémentaires à récupérer lors d'un RETURNING.
     * column name => [map from DB column to PHP attribute value, attribute name, PDO param type]
     * @return array<string, array{?callable(mixed): mixed, string, int}>
     */
    protected static function computed_fields(): array { return []; }

    function __get(string $name): mixed
    {
        if (
            in_array($name, array_column(static::key_fields(), 1), true) ||
            in_array($name, array_column(static::computed_fields(), 1), true)
        ) {
            return $this->$name;
        }
        throw new Exception('Undefined property: ' . static::class . "::\$$name");
    }

    function push_to_db(): void
    {
        if ($this->exists_in_db()) {
            $returning_fields = static::computed_fields();
            $stmt = DB\update(
                static::TABLE,
                $this->args(),
                $this->key_args(),
                array_keys($returning_fields),
            );
        } else {
            $returning_fields = static::key_fields() + static::computed_fields();
            $stmt = DB\insert_into(
                static::TABLE,
                $this->args(),
                array_keys($returning_fields),
            );
        }

        notfalse($stmt->execute());

        if ($returning_fields) {
            $row = notfalse($stmt->fetch());
            foreach ($returning_fields as $column => [$db_to_php, $attr, $type]) {
                $this->$attr = $db_to_php === null ? $row[$column] : $db_to_php($row[$column]);
            }
        }
    }

    function delete(): void
    {
        if (!$this->exists_in_db()) return;

        $stmt = DB\delete_from(
            static::TABLE,
            $this->key_args()
        );
        notfalse($stmt->execute());

        foreach (array_column(static::key_fields(), 1) as $attr) {
            $this->$attr = null;
        }
    }

    private function exists_in_db(): bool
    {
        return array_every(static::key_fields(), fn($f) => $this->{$f[1]} !== null);
    }

    private function key_args(): array
    {
        $args = [];
        foreach (static::key_fields() as $column => [$db_to_php, $attr, $type]) {
            $args[$column] = [$this->$attr, $type];
        }
        return $args;
    }

    /**
     * @return array<string, array{mixed, int}>
     */
    private function args(): array
    {
        $args = [];
        foreach (static::fields() as $column => [$php_to_db, $attr, $type]) {
            $args[$column] = [$php_to_db === null ? $this->$attr : $php_to_db($this->$attr), $type];
        }
        return $args;
    }
}