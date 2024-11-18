<?php
session_start();

require_once 'queries.php';
require_once 'auth.php';
require_once 'util.php';
require_once 'const.php';
require_once 'component/input_address.php';
require_once 'component/head.php';

?><pre><?= htmlspecialchars(print_r($_GET, true)) ?></pre><?php
?><pre><?= htmlspecialchars(print_r($_POST, true)) ?></pre><?php
?><pre><?= htmlspecialchars(print_r($_FILES, true)) ?></pre><?php

$args = [
    'type_offre' => getarg($_GET, 'type_offre', arg_check(f_str_is(array_keys(TYPES_OFFRE)))),
];

$id_professionnel = exiger_connecte_pro();

if ($_POST) {
    $args += [
        'adresse_commune' => getarg($_POST, 'adresse_commune'),
        'description_detaillee' => getarg($_POST, 'description_detaillee'),
        'resume' => getarg($_POST, 'resume'),
        'titre' => getarg($_POST, 'titre'),
        'adresse_complement_numero' => getarg($_POST, 'adresse_complement_numero', required: false),
        'adresse_localite' => getarg($_POST, 'adresse_localite', required: false),
        'adresse_nom_voie' => getarg($_POST, 'adresse_nom_voie', required: false),
        'adresse_numero_voie' => getarg($_POST, 'adresse_numero_voie', required: false),
        'adresse_precision_ext' => getarg($_POST, 'adresse_precision_ext', required: false),
        'adresse_precision_int' => getarg($_POST, 'adresse_precision_int', required: false),
        'url_site_web' => getarg($_POST, 'url_site_web', required: false),
        'file_gallerie' => getarg($_FILES, 'gallerie'),
        'file_image_principale' => getarg($_FILES, 'image_principale'),
    ];

    $args += match ($args['type_offre']) {
        'activite' => [
            'indication_duree' => getarg($_POST, 'indication_duree'),
            'prestations_incluses' => getarg($_POST, 'prestations_incluses'),
            'age_requis' => getarg($_POST, 'age_requis', arg_filter(FILTER_VALIDATE_INT, ['min_range' => 1]), required: false),
            'prestations_non_incluses' => getarg($_POST, 'prestations_non_incluses')
        ],
        'parc-attractions' => [
            'file_image_plan' => getarg($_FILES, 'image_plan'),
        ],
        'spectacle' => [
            'indication_duree' => getarg($_POST, 'indication_duree'),
            'capacite_accueil' => getarg($_POST, 'capacite_accueil', arg_filter(FILTER_VALIDATE_INT, ['min_range' => 0])),
        ],
        'restaurant' => [
            'carte' => getarg($_POST, 'carte'),
            'richesse' => getarg($_POST, 'richesse'),
            'sert_petit_dejeuner' => getarg($_POST, 'sert_petit_dejeuner', required: false),
            'sert_brunch' => getarg($_POST, 'sert_brunch', required: false),
            'sert_dejeuner' => getarg($_POST, 'sert_dejeuner', required: false),
            'sert_diner' => getarg($_POST, 'sert_diner', required: false),
            'sert_boissons' => getarg($_POST, 'sert_boissons', required: false),
        ],
        'visite' => [
            'indication_duree' => getarg($_POST, 'indication_duree')
        ]
    };

    // Délégation du traitement à un autre script pour gagner de la place
    require 'traitement/creation_offre.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<?php put_head("Création d'une offre", ['creation_offre.css'], ['creation_offre.js' => 'defer']) ?>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <section id="titre-creation-offre">
            <h1>Créer <?= TYPES_OFFRE[$args['type_offre']] ?></h1>
        </section>
        <section id="info-generales">
            <h2>Informations générales</h2>
            <div>
                <label for="titre">Titre*</label>
                <p>
                    <input form="f" id="titre" name="titre" type="text" required>
                </p>
                <label for="resume">Resumé*</label>
                <p>
                    <input form="f" id="resume" name="resume" type="text" required>
                </p>
                <label for="adresse">Adresse*</label>
                <p>
                    <?php
                    put_input_address('f')
                    ?>
                </p>
                <label for="site">Site Web</label>
                <p>
                    <input form="f" id="url_site_web" name="url_site_web" type="url">
                </p>
            </div>
        </section>
        <section>
            <h2>Photo principale</h2>
            <input form="f" id="image_principale" name="image_principale" type="file" accept="image/*" required>
            <div id="image_principale-preview"></div>
        </section>
        <section id="tarif">
            <h2>Tarifs</h2>
            <table id="table-tarifs">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <td><input form="f" id="tarif-montant" type="text" minlength="1" placeholder="Enfant, Sénior&hellip;"></td>
                        <td><input form="f" id="tarif-nom" type="number" min="0" placeholder="Prix"> €</td>
                        <td><button id="button-add-tarif" type="button">+</button></td>
                    </tr>
                </tfoot>
            </table>
        </section>
        <section id="horaires">
            <h2>Horaires</h2>
            <div>
                <?php foreach (JOURS_SEMAINE as $jour) { ?>
                <article id="<?= $jour ?>">
                    <h3><?= ucfirst($jour) ?></h3>
                    <button id="button-add-horaire-<?= $jour ?>" type="button">+</button>
                    <table id="table-horaires-<?= $jour ?>">
                        <thead>
                            <tr>
                                <th>Début</th>
                                <th>Fin</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </article>
                <?php } ?>
            </div>
        </section>
        <section id="tags">
            <h2>Tags</h2>
            <ul id="list-tag">
                <?php
                foreach ($args['type_offre'] === 'restaurant' ? TAGS_RESTAURANT : DEFAULT_TAGS as $tag) {
                    ?>
                    <li><label><input form="f" name="tags[<?= $tag ?>]" type="checkbox"><?= $tag ?></label></li>
                <?php } ?>
            </ul>
        </section>
        <section id="description_detaillee">
            <h2>Description</h2>
            <textarea form="f" id="description_detaillee" name="description_detaillee" required></textarea>
        </section>
        <section id="image-creation-offre">
            <h2>Gallerie</h2>
            <input form="f" id="gallerie" name="gallerie[]" type="file" accept="image/*" multiple>
            <div id="gallerie-preview"></div>
        </section>
        <section id="infos-detaillees">
            <h2>Informations détaillées</h2>
            <?php
            switch ($args['type_offre']) {
                case 'activite':
                    ?>
                    <p><label>Âge requis&nbsp;: <input form="f" name="age_requis" type="number" min="1"> an</label></p>
                    <p>Prestations incluses*</p>
                    <textarea form="f" name="prestations_incluses" required></textarea>
                    <p>Prestations non incluses</p>
                    <textarea form="f" name="prestations_non_incluses"></textarea>
                    <?php
                    put_input_indication_duree();
                    break;
                case 'parc-attractions':
                    ?>
                    <p><label>Âge requis&nbsp;: <input form="f" name="age_requis" type="number" min="1"> an</label></p>
                    <fieldset>
                        <p><label>Plan* &nbsp;: <input form="f" id="image_plan" name="image_plan" type="file" accept="image/*" required></label></p>
                        <div id="image_plan-preview"></div>
                    </fieldset>
                    <?php
                    break;
                case 'restaurant':
                    ?>
                    <fieldset>
                        <legend>Niveau de richesse</legend>
                        <p><label><input form="f" type="radio" name="richesse" value="1" checked> €</label></p>
                        <p><label><input form="f" type="radio" name="richesse" value="2"> €€</label></p>
                        <p><label><input form="f" type="radio" name="richesse" value="3"> €€€</label></p>
                    </fieldset>
                    <fieldset>
                        <legend>Repas servis</legend>
                        <p><label><input form="f" type="checkbox" name="sert_petit_dejeuner">Petit déjeuner</label></p>
                        <p><label><input form="f" type="checkbox" name="sert_brunch">Brunch</label></p>
                        <p><label><input form="f" type="checkbox" name="sert_dejeuner">Déjeuner</label></p>
                        <p><label><input form="f" type="checkbox" name="sert_diner">Dîner</label></p>
                        <p><label><input form="f" type="checkbox" name="sert_boissons">Boissons</label></p>
                    </fieldset>
                    <p>Carte</p>
                    <textarea form="f" name="carte"></textarea>
                    <?php
                    break;
                case 'spectacle':
                    ?>
                    <p><label>Capacité d'accueil&nbsp;: <input form="f" name="capacite_accueil" type="number" min="0" required></label> pers.</p>
                    <?php
                    put_input_indication_duree();
                    break;
                case 'visite':
                    put_input_indication_duree();
                    break;
            }
            ?>
        </section>
        <form id="f" method="post" enctype="multipart/form-data">
            <button type="submit">Valider</button>
        </form>
    </main>
    <?php require 'component/footer.php' ?>
    <template id="template-tarif-tr">
        <tr>
            <td><input form="f" name="tarifs_nom[]" type="text" minlength="1" placeholder="Enfant, Sénior&hellip;" required readonly></td>
            <td><input form="f" name="tarifs_montant[]" type="number" min="0" placeholder="Prix" required> €</td>
            <td><button type="button">-</button></td>
        </tr>
    </template>
    <?php foreach (JOURS_SEMAINE as $jour) { ?>
    <template id="template-horaire-tr-<?= $jour ?>">
        <tr>
            <td><input form="f" name="horaires_debut[<?= $jour ?>][]" type="time" required></td>
            <td><input form="f" name="horaires_fin[<?= $jour ?>][]" type="time" required></td>
            <td><button type="button">-</button></td>
        </tr>
    </template>
    <?php } ?>
</body>

</html>
<?php
function put_input_indication_duree()
{
    ?>
        <label>Durée estimée&nbsp;: <input-duration id='indication_duree'></input-duration></label>
        <?php
}
