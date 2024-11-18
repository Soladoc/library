<?php
require_once 'db.php';

function put_input_address(string $prefix = '', string $form_id = '')
{
    $form_attr = $form_id === '' ? '' : "form=\"$form_id\"";
    $communes = db_connect()->query('select nom from _commune fetch first 1000 rows only')->fetchAll()
?>
<details class="input-address">
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

function put_input_duration(string $prefix = '', string $form_id = '')
{
    $form_attr = $form_id === '' ? '' : "form=\"$form_id\"";
    ?>
    <p class="input-duration">
        <input <?= $form_attr ?> id="days" name="days" type="number" min="0" required> jour(s)
        <input <?= $form_attr ?> id="hours" name="hours" type="number" required> heure(s)
        <input <?= $form_attr ?> id="minutes" name="minutes" type="number" required> minute(s)
    </p>
<?php
}
