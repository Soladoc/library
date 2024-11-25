<?php
require_once 'db.php';

/**
 * Affiche le composant d'input d'adresse postale.
 *
 * Noms dans le POST (optionellement suffixés par $prefix)
 *
 * - `commune`
 * - `localite`
 * - `nom_voie`
 * - `numero_voie`
 * - `complement_numero`
 * - `precision_int`
 * - `precision_ext`
 *
 * @param string $form_id l'ID du formulaire auquel appartient le contrôle. Pas nécéssaire de le spécifier si l'élément est déjà dans un `<form>`.
 * @param string $id L'ID de l'élément à ajouter. Optionnel, ne pas spécifier pour pas d'ID.
 * @param string $prefix Le préfixe des attributs "name" des champs pour chaque champ de l'adresse. Définit les noms de clés dans le $_POST en PHP. Optionnel, ne pas spécifier pour pas de préfix
 * @return void
 */
function put_input_address(string $id = '', string $prefix = '', string $form_id = '')
{
    $form_attr = $form_id ? "form=\"$form_id\"" : '';
    $communes = DB\connect()->query('select nom from _commune fetch first 1000 rows only')->fetchAll()
?>
<details <?= $id ? "id=\"$id\"" : '' ?> class="input-address">
    <datalist id="datalist-input-address-communes"><?php
    foreach ($communes as $c) {
?><option><?= $c['nom'] ?></option><?php
    }
?></datalist>
    <summary>
        <input <?= $form_attr ?> type="text" readonly>
    </summary>
    <p><label>Commune&nbsp;: <input <?= $form_attr ?> id="<?= $prefix ?>commune" name="<?= $prefix ?>commune" type="text" list="datalist-input-address-communes" autocomplete="on" required></label></p>
    <p><label>Localité&nbsp;: <input <?= $form_attr ?> id="<?= $prefix ?>localite" name="<?= $prefix ?>localite" type="text" maxlength="255" placeholder="hameau, lieu-dit&hellip; (optionnel)"></label></p>
    <p><label>Nom voie&nbsp;: <input <?= $form_attr ?> id="<?= $prefix ?>nom_voie" name="<?= $prefix ?>nom_voie" type="text" maxlength="255" placeholder="rue de l'Église&hellip; (optionnel)"></label></p>
    <p><label>Numéro voie&nbsp;: <input <?= $form_attr ?> id="<?= $prefix ?>numero_voie" name="<?= $prefix ?>numero_voie" type="number" min="1" placeholder="1,2&hellip; (optionnel)"></label></p>
    <p><label>Complément numéro&nbsp;: <input <?= $form_attr ?> id="<?= $prefix ?>complement_numero" name="<?= $prefix ?>complement_numero" type="text" maxlength="10" placeholder="bis, ter&hellip; (optionnel)"></label></p>
    <p><label>Précision interne&nbsp;: <input <?= $form_attr ?> id="<?= $prefix ?>precision_int" name="<?= $prefix ?>precision_int" type="text" maxlength="255" placeholder="apt., boîte à lettre, étage (optionnel)&hellip;"></label></p>
    <p><label>Précision externe&nbsp;: <input <?= $form_attr ?> id="<?= $prefix ?>precision_ext" name="<?= $prefix ?>precision_ext" type="text" maxlength="255" placeholder="bâtiment, voie, résidence (optionnel)&hellip;"></label></p>
</details>
<?php
}

function put_input_duration(string $form_id = '', string $id = '', string $prefix = '')
{
    $form_attr = $form_id ? "form=\"$form_id\"" : '';
    ?>
    <p <?= $id ? "id=\"$id\"" : '' ?> class="input-duration">
        <input <?= $form_attr ?> id="<?= $prefix ?>jours" name="<?= $prefix ?>jours" value="0" type="number" min="0" required> jour(s)
        <input <?= $form_attr ?> id="<?= $prefix ?>heures" name="<?= $prefix ?>heures" value="0" type="number" min="0" max="24" required> heure(s)
        <input <?= $form_attr ?> id="<?= $prefix ?>minutes" name="<?= $prefix ?>minutes" value="0" type="number" min="0" max="60" required> minute(s)
    </p>
<?php
}
