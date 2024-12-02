<?php
require_once 'util.php';


/**
 * @extends Input<string[]>
 */
final class DynamicTable extends Input {

    /**
     * @var callable(DynamicTable, ?string[]): void
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
     * @param callable(DynamicTable, ?string[]): void $put_row
     * @param callable(DynamicTable): void $put_prompt
     * @param string[] $columns
     * @param string $id
     * @param string $name
     * @param string $form_id
     */
    function __construct(array $columns, callable $put_row, callable $put_prompt, string $id, string $name = '', string $form_id = '') {
        parent::__construct($id, $name, $form_id);
        $this->put_row = Closure::fromCallable($put_row);
        $this->put_prompt = Closure::fromCallable($put_prompt);
        $this->columns = $columns;
    }

    /**
     * @param array $get_or_post
     * @param bool $required
     * @return ?string[]
     */
    function get(array $get_or_post, bool $required = true): ?array {
        $rows = getarg($get_or_post, $this->name, required: $required);
        if ($rows === null) return null;
        return soa_to_aos($rows);
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