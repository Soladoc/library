<?php
require_once 'db.php';

function put_input_address(?string $form_id = null, string $name = 'adresse')
{
    $form_attr = $form_id === null ? '' : "form=\"$form_id\"";
    $pdo = db_connect();
    $communes = $pdo->query('select code_insee, code_postal, nom from pact._commune fetch first 1000 rows only')->fetchAll() ?>
<details>
    <datalist id="datalist-input-address-communes"><?php
    foreach ($communes as $c) {
?><option><?= $c['nom'] ?></option><?php
    }
?></datalist>
    <summary>
        <input <?= $form_attr ?> type="text" readonly>
    </summary>
    <p><label>Commune&nbsp;: <input <?= $form_attr ?> name="<?= $name ?>[commune]" type="text" list="datalist-input-address-communes" autocomplete="on" required></label></p>
    <p><label>Localité&nbsp;: <input <?= $form_attr ?> name="<?= $name ?>[localite]" type="text" maxlength="255" placeholder="hameau, lieu-dit&hellip;"></label></p>
    <p><label>Nom voie&nbsp;: <input <?= $form_attr ?> name="<?= $name ?>[nom_voie]" type="text" maxlength="255" placeholder="rue de l'Église&hellip; (optionnel)"></label></p>
    <p><label>Numéro voie&nbsp;: <input <?= $form_attr ?> name="<?= $name ?>[num_voie]" type="number" min="1" placeholder="1,2&hellip; (optionnel)"></label></p>
    <p><label>Complément numéro&nbsp;: <input <?= $form_attr ?> name="<?= $name ?>[compl_numero]" type="text" maxlength="10" placeholder="bis, ter&hellip; (optionnel)"></label></p>
    <p><label>Précision interne&nbsp;: <input <?= $form_attr ?> name="<?= $name ?>[precision_int]" type="text" maxlength="255" placeholder="apt., boîte à lettre, étage&hellip;"></label></p>
    <p><label>Précision externe&nbsp;: <input <?= $form_attr ?> name="<?= $name ?>[precision_ext]" type="text" maxlength="255" placeholder="bâtiment, voie, résidence&hellip;"></label></p>
</details>
<?php }
