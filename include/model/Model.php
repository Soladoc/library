<?php
require_once 'db.php';

abstract class Model
{
    /**
     * Stuff that you can set.
     * attribute name => [Attribute name on attribute to get the column value, or null for identity, column name, PDO param type]
     * @var array<string, array{?string, string, int}[]>
     */
    protected const FIELDS = [];  // abstract constant

    /**
     * Table name.
     * @var string
     */
    const TABLE = null;  // abstract constant

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
    protected static function insert_fields() { return []; }

    function insert(): void
    {
        if ($this->exists_in_db()) {
            throw new LogicException('This model already exists in the DB.');
        }
        $returning_fields = static::key_fields() + static::insert_fields();
        $stmt = DB\insert_into(
            static::TABLE,
            $this->insert_args(),
            array_column($returning_fields, 0),
        );
        notfalse($stmt->execute());
        $row = notfalse($stmt->fetch());
        foreach ($returning_fields as $attr => [$column, $type, $db_to_php]) {
            $this->$attr = $db_to_php === null ? $row[$column] : $db_to_php($row[$column]);
        }
    }

    function __get(string $name): mixed
    {
        if (isset(static::key_fields()[$name]) && $this->$name === null) {
            $this->insert();
        }
        return $this->$name;
    }

    function __set(string $name, mixed $value): void
    {
        if ($this->exists_in_db()) {
            $stmt = DB\update(static::TABLE, $this->update_args($name, $value), $this->key_args());
            notfalse($stmt->execute());
        }
        $this->$name = $value;
    }

    function delete(): void
    {
        if (!$this->exists_in_db()) {
            throw new LogicException('This model must exist in the DB');
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
    private function insert_args(): array
    {
        $args = [];
        foreach (static::FIELDS as $attr => $fields) {
            foreach ($fields as [$sub_attr, $column, $type]) {
                $args[$column] = [$this->get_value($this->__get($attr), $sub_attr), $type];
            }
        }
        return $args;
    }

    /**
     * Summary of update_arg
     * @return array<string, array{mixed, int}>
     */
    private function update_args(string $attr, mixed $value): array
    {
        $args = [];
        foreach (static::FIELDS[$attr] as [$sub_attr, $column, $type]) {
            $args[] = [$column => [$this->get_value($value, $sub_attr), $type]];
        }
        return $args;
    }

    private function get_value(mixed $value, ?string $sub_attr)
    {
        return $sub_attr === null
            ? $value
            : ($value instanceof Model
                ? $value->__get($sub_attr)
                : $value->$sub_attr);
    }
}
