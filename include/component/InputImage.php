<?php
require_once 'util.php';
require_once 'component/Input.php';
require_once 'model/Image.php';

/**
 * @extends Input<ImageView>
 */
final class InputImage extends Input
{
    readonly string $fieldset_legend;

    function __construct(string $fieldset_legend, string $id = '', string $name = '', string $form_id = '')
    {
        parent::__construct($id, $name, $form_id);
        $this->fieldset_legend = $fieldset_legend;
    }

    /**
     * @inheritDoc
     */
    function getarg(array $get_or_post, bool $required = true): ?Image
    {
        $file = getarg($_FILES, $this->name, required: $required);
        return $file === null ? $file : Image::from_input(
            $file['size'],
            explode('/', $file['type'], 2)[1],
            $file['tmp_name'],
            getarg($get_or_post, "{$this->name}_legende", required: false),
        );
    }

    /**
     * @inheritDoc
     */
    function put($current = null): void
    {
        $form_attr = $this->form_id ? "form=\"$this->form_id\"" : '';
?>
<fieldset id="<?= $this->id ?>" class="input-image">
    <legend><?= $this->fieldset_legend ?></legend>
    <p>
        <input <?= $form_attr ?>
            name="<?= $this->name ?>"
            type="file"
            accept="image/*"
            required>
    </p>
    <p>
        <input <?= $form_attr ?>
            id="<?= $this->id ?>_legende"
            name="<?= $this->name ?>_legende"
            type="text"
            placeholder="Légende">
    </p>
    <div id="<?= $this->id ?>-preview">
        <?php $current?->put_img() ?>
    </div>
</fieldset>
<?php
    }
}
