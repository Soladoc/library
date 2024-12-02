<?php
require_once 'util.php';
require_once 'component/Input.php';
require_once 'model/Image.php';

final class InputImage extends Input
{
    readonly string $fieldset_legend;

    function __construct(string $fieldset_legend, string $id = '', string $name = '', string $form_id = '')
    {
        parent::__construct($id, $name, $form_id);
        $this->fieldset_legend = $fieldset_legend;
    }

    /**
     * Récupère l'image saisie.
     * @param array $get_or_post `$_GET` ou `$_POST` (selon la méthode du formulaire)
     * @param ?int $current_id_image L'ID de l'image à modifier ou `null` pour une création.
     * @param bool $required Si l'image est requise. Quand l'image est manquante, si `false` a été passé, la fonciton retourne `null`. Sinon, déclenche une erreur.
     */
    function get(array $get_or_post, ?int $current_id_image = null, bool $required = true): ?Image
    {
        $file = getarg($_FILES, $this->name, required: $required);
        return $file === null ? $file : new Image(
            $current_id_image,
            getarg($file, 'size', arg_int()),
            explode('/', $file['type'], 2)[1],
            getarg($get_or_post, "{$this->name}_legende", required: false),
            $file['tmp_name'],
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
