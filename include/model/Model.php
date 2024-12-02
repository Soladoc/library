<?php
require_once 'db.php';

abstract class Model
{
    /**
     * Stuff that you can set.
     * attribute name => [Attribute name on attribute to get the column value, or null for identity, column name, PDO param type]
     * @var array<string, array{?string, string, int}>
     */
    protected const FIELDS = null; // abstract constant

    /**
     * Table name.
     * @var string
     */
    const TABLE = null; // abstract constant

    /**
     * Key fields that uniquely identify a row in the DB table.
     * attribute name => [column name, PDO param type]
     * @var array<string, array{string, int}>
     */
    protected const KEY_FIELDS = null;  // abstract constant

    /**
     * Additional fields to set with the insertion RETURNING clause.
     * attribute name => [column name, PDO param type]
     * @var array<string, array{string, int}>
     */
    protected const INSERT_FIELDS = [];

    function insert(): void
    {
        if ($this->exists_in_db()) {
            throw new LogicException('This model already exists in the DB.');
        }
        $returning_fields = static::KEY_FIELDS + static::INSERT_FIELDS;
        $stmt = DB\insert_into(
            static::TABLE,
            $this->insert_args(),
            array_column($returning_fields, 0),
        );
        notfalse($stmt->execute());
        $row = notfalse($stmt->fetch());
        foreach ($returning_fields as $attr => [$column, $_]) {
            $this->$attr = $row[$column];
        }
    }

    function __get(string $name): mixed
    {
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
        foreach (array_keys(static::KEY_FIELDS) as $attr) {
            $this->$attr = null;
        }
    }

    private function exists_in_db(): bool
    {
        return array_every(array_keys(static::KEY_FIELDS), fn($attr) => $this->$attr !== null);
    }

    private function key_args(): array
    {
        $args = [];
        foreach (static::KEY_FIELDS as $attr => [$column, $type]) {
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
        foreach (static::FIELDS as $attr => [$sub_attr, $column, $type]) {
            $args[$column] = [$sub_attr === null ? $this->$attr : $this->attr->$sub_attr, $type];
        }
        return $args;
    }

    /**
     * Summary of update_arg
     * @return array<string, array{mixed, int}>
     */
    private function update_args(string $attr, mixed $value): array
    {
        [$sub_attr, $column, $type] = static::FIELDS[$attr];
        return [$column => [$sub_attr === null ? $value : $value->$sub_attr, $type]];
    }
}
