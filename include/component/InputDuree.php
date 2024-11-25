<?php
require_once 'util.php';
require_once 'component/Input.php';
require_once 'model/Duree.php';

/**
 * Composant d'input de durée (jours, heures, minutes).
 * Demander à Raphaël si besoin de d'autres unités.
 * @extends Input<Duree>
 */
final class InputDuree extends Input
{
    function __construct(string $id = '', string $name = '', string $form_id = '')
    {
        parent::__construct($id, $name, $form_id);
    }

    /**
     * @inheritDoc
     */
    function getarg(array $get_or_post, bool $required = true): ?Duree {
        $data = getarg($get_or_post, $this->name, required: $required);
        return $data === null ? null : Duree::from_input($data);
    }

    /**
     * @inheritDoc
     */
    function put($current): void
    {
        $form_attr = $this->form_id ? "form=\"$this->form_id\"" : '';
?>
<p <?= $this->id ? "id=\"$this->id\"" : '' ?> class="input-duration">
    <input <?= $form_attr ?>
        id="<?= $this->id ?>_jours"
        name="<?= $this->name ?>[jours]"
        type="number"
        min="0"
        required
        value="<?= $current?->jours ?? 0 ?>"> jour(s)
    <input <?= $form_attr ?>
        id="<?= $this->id ?>_heures"
        name="<?= $this->name ?>[heures]"
        type="number"
        min="0"
        max="24"
        required
        value="<?= $current?->heures ?? 0 ?>"> heure(s)
    <input <?= $form_attr ?>
        id="<?= $this->id ?>_minutes"
        name="<?= $this->name ?>[minutes]"
        type="number"
        min="0"
        max="60"
        required
        value="<?= $current?->minutes ?? 0 ?>" > minute(s)
</p>
<?php
    }
}
