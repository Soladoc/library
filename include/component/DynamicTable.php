<?php
require_once 'util.php';


/**
 * @template T
 * @extends Input<T[]>
 */
final class DynamicTable extends Input {

    /**
     * @var callable(DynamicTable, ?T): void
     */
    private readonly Closure $put_row;

    /**
     * @var callable(DynamicTable): void
     */
    private readonly Closure $put_prompt;

    /**
     * @var string[]
     */
    private readonly array $columns;

    /**
     * @var callable(array): T
     */
    private readonly Closure $get;

    /**
     * @param callable(DynamicTable, ?T): void $put_row
     * @param callable(DynamicTable): void $put_prompt
     * @param callable(array): T $get
     * @param string[] $columns
     * @param string $id
     * @param string $name
     * @param string $form_id
     */
    function __construct(array $columns, callable $put_row, callable $put_prompt, callable $get, string $id, string $name = '', string $form_id = '') {
        parent::__construct($id, $name, $form_id);
        $this->put_row = Closure::fromCallable($put_row);
        $this->put_prompt = Closure::fromCallable($put_prompt);
        $this->get = Closure::fromCallable($get);
        $this->columns = $columns;
    }

    /**
     * @param array $get_or_post
     * @param bool $required
     * @return T[]
     */
    function get(array $get_or_post, bool $required = true): ?array {
        $rows = getarg($get_or_post, $this->name, required: $required);
        if ($rows === null) return null;
        array_map($this->get, soa_to_aos($rows));
    }

    /**
     * @inheritDoc
     */
    function put(mixed $current = null): void {
        $form_attr = $this->form_id ? "form=\"$this->form_id\"" : '';
?>
<table id="<?= $this->id ?>">
    <thead>
        <?php foreach ($this->columns as $column) { ?>
            <th><?= $column ?></th>
        <?php } ?>
    </thead>
    <tbody>
        <?php foreach ($current ?? [] as $row) {
            ($this->put_row)($this, $row);
        } ?>
    </tbody>
    <tfoot>
        <tr>
            <?= ($this->put_prompt)($this); ?>
        </tr>
    </tfoot>
</table>
<template id="template-<?= $this->id ?>-tr"><tr>
    <?php ($this->put_row)($this, null) ?>
</tr></template>
<?php
    }
}