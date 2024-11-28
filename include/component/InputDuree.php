<?php
require_once 'util.php';
require_once 'component/Input.php';
require_once 'model/Duree.php';

/**
 * Composant d'input de durée (jours, heures, minutes).
 * Demander à Raphaël si besoin de d'autres unités.
 */
final class InputDuree extends Input
{
    function __construct(string $id = '', string $name = '', string $form_id = '')
    {
        parent::__construct($id, $name, $form_id);
    }

    /**
     * Récupère l'adresse saisie.
     * @param array $get_or_post `$_GET` ou `$_POST` (selon la méthode du formulaire)
     * @param bool $required Si la durée est requise. Quand la durée est manquante, si `false` a été passé, la fonciton retourne `null`. Sinon, déclenche une erreur.
     */
    function get(array $get_or_post, bool $required = true): ?Duree
    {
        $data = getarg($get_or_post, $this->name, required: $required);
        return $data === null ? null : new Duree(
            getarg($data, 'jours', arg_filter(FILTER_VALIDATE_INT)),
            getarg($data, 'heures', arg_filter(FILTER_VALIDATE_INT)),
            getarg($data, 'minutes', arg_filter(FILTER_VALIDATE_INT)),
        );
    }

    /**
     * Affiche l'HTML du composant.
     * @param ?Duree $current La duréee à modifier ou `null` pour une création.
     */
    function put(?Duree $current = null): void
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
