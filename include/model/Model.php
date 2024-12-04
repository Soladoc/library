<?php
require_once 'db.php';

abstract class Model
{
    /**
     * Stuff that you can set.
     * column name => [Attribute name on attribute to get the column value, or `null` for identity, attribute name, PDO param type]
     * @var array<string, array{?string, string, int}>
     */
    protected const FIELDS = [];

    /**
     * Table name.
     * @var string
     */
    const TABLE = self::TABLE; // abstract constant

    /**
     * Key fields that uniquely identify a row in the DB table.
     * attribute name => [column name, PDO param type]
     * @return array<string, array{string, int, ?callable(string): mixed}>
     */
    protected static function key_fields() { return []; }

    /**
     * Additional fields to set with the insertion RETURNING clause.
     * attribute name => [column name, PDO param type]
     * @return array<string, array{string, int, ?callable(string): mixed}>
     */
    protected static function computed_fields() { return []; }

    function __get(string $name): mixed
    {
        if (isset($this->key_fields()[$name]) || isset($this->computed_fields()[$name])) {
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
                $returning_fields,
            );
        } else {
            $returning_fields = static::key_fields() + static::computed_fields();
            $stmt = DB\insert_into(
                static::TABLE,
                $this->args(),
                array_column($returning_fields, 0),
            );
        }
        notfalse($stmt->execute());
        $row = notfalse($stmt->fetch());
        foreach ($returning_fields as $attr => [$column, $type, $db_to_php]) {
            $this->$attr = $db_to_php === null ? $row[$column] : $db_to_php($row[$column]);
        }
    }

    function delete(): void
    {
        if (!$this->exists_in_db()) {
            return;
        }
        $stmt = DB\delete_from(
            static::TABLE,
            $this->key_args(),
        );
        notfalse($stmt->execute());
        foreach (array_keys(static::key_fields()) as $attr) {
            $this->$attr = null;
        }
    }

    private function exists_in_db(): bool
    {
        return array_every(array_keys(static::key_fields()), fn($attr) => $this->$attr !== null);
    }

    private function key_args(): array
    {
        $args = [];
        foreach (static::key_fields() as $attr => [$column, $type, $db_to_php]) {
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
        foreach (static::FIELDS as $column => [$sub_attr, $attr, $type]) {
            $args[$column] = [$this->get_value($this->$attr, $sub_attr), $type];
        }
        return $args;
    }

    private function get_value(mixed $value, ?string $sub_attr)
    {
        return $sub_attr === null
            ? $value
            : $value->$sub_attr;
    }
}
