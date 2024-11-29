<?php

require_once 'queries.php';
require_once 'auth.php';
require_once 'util.php';
require_once 'const.php';
require_once 'component/inputs.php';
require_once 'component/Page.php';
require_once 'component/InputDuree.php';

$page = new Page("Modifier offre",
    ['creation_offre.css'],
    ['module/creation_offre.js' => 'defer type="module"']);

$id_professionnel = Auth\exiger_connecte_pro();
$est_prive = DB\exists_pro_prive($id_professionnel);


$offre = DB\query_offre($_GET['id']);
print_r($offre);

$args = [
    // ne lance pas la page et génère une errreur si il n'y a pas de get
    'type_offre' => getarg($_GET, 'type_offre', arg_check(f_is_in(array_keys(CATEGORIES_OFFRE)))),
];



if ($_POST) {
    $args += [
        'adresse_commune' => ucfirst(getarg($_POST, 'adresse_commune')),
        'adresse_complement_numero' => getarg($_POST, 'adresse_complement_numero', required: false),
        'description_detaillee' => getarg($_POST, 'description_detaillee'),
        'horaires' => getarg($_POST, 'horaires', required: false) ?? ['debut' => [], 'fin' => []],
        'periodes' => getarg($_POST, 'periodes', required: false) ?? ['debut' => [], 'fin' => []],
        'resume' => getarg($_POST, 'resume'),
        'tags' => getarg($_POST, 'tags', arg_filter(FILTER_DEFAULT, FILTER_REQUIRE_ARRAY), required: false) ?? [],
        'tarifs' => getarg($_POST, 'tarifs', required: false) ?? [],
        'titre' => getarg($_POST, 'titre'),
        'adresse_localite' => getarg($_POST, 'adresse_localite', required: false),
        'adresse_nom_voie' => getarg($_POST, 'adresse_nom_voie', required: false),
        'adresse_numero_voie' => getarg($_POST, 'adresse_numero_voie', arg_filter(FILTER_VALIDATE_INT, ['min_range' => 1]), required: false),
        'adresse_precision_ext' => getarg($_POST, 'adresse_precision_ext', required: false),
        'adresse_precision_int' => getarg($_POST, 'adresse_precision_int', required: false),
        'url_site_web' => getarg($_POST, 'url_site_web', required: false),
        'libelle_abonnement' => getarg($_POST, 'libelle_abonnement', required: false) ?? 'gratuit',
        'file_gallerie' => getarg($_FILES, 'gallerie'),
        'file_image_principale' => getarg($_FILES, 'image_principale'),
    ];

    function indication_duree_args(): array
    {
        return [
            'indication_duree_jours' => getarg($_POST, 'indication_duree_jours', arg_filter(FILTER_VALIDATE_INT, ['min_range' => 0])),
            'indication_duree_heures' => getarg($_POST, 'indication_duree_heures', arg_filter(FILTER_VALIDATE_INT, ['min_range' => 0])),
            'indication_duree_minutes' => getarg($_POST, 'indication_duree_minutes', arg_filter(FILTER_VALIDATE_INT, ['min_range' => 0])),
        ];
    }

    $args += match ($args['type_offre']) {
        'activité' => indication_duree_args() + [
            'age_requis' => getarg($_POST, 'age_requis', arg_filter(FILTER_VALIDATE_INT, ['min_range' => 1]), required: false),
            'prestations_incluses' => getarg($_POST, 'prestations_incluses'),
            'prestations_non_incluses' => getarg($_POST, 'prestations_non_incluses', required: false)
        ],
        'parc d\'attractions' => [
            'file_image_plan' => getarg($_FILES, 'image_plan'),
        ],
        'spectacle' => indication_duree_args() + [
            'capacite_accueil' => getarg($_POST, 'capacite_accueil', arg_filter(FILTER_VALIDATE_INT, ['min_range' => 0])),
        ],
        'restaurant' => [
            'carte' => getarg($_POST, 'carte'),
            'richesse' => getarg($_POST, 'richesse'),
            'sert_boissons' => getarg($_POST, 'sert_boissons', required: false),
            'sert_brunch' => getarg($_POST, 'sert_brunch', required: false),
            'sert_dejeuner' => getarg($_POST, 'sert_dejeuner', required: false),
            'sert_diner' => getarg($_POST, 'sert_diner', required: false),
            'sert_petit_dejeuner' => getarg($_POST, 'sert_petit_dejeuner', required: false),
        ],
        'visite' => indication_duree_args()
    };

    // Délégation du traitement à un autre script pour gagner de la place
    require 'traitement/creation_offre.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>
        <h1>Modifier <?= CATEGORIES_OFFRE[$args['type_offre']] ?></h1>

        <section id="type-abonnement">
            <h2>Abonnement</h2>
            <?php if ($est_prive) { ?> 
            <ul id="liste-choix-abonnement">
                <li>
                    <label><input form="f"
                        name="libelle_abonnement"
                        value="standard"
                        type="radio"> Standard</label>
                </li>
                <li>
                    <label><input form="f"
                        name="libelle_abonnement"
                        value="premium"
                        type="radio"> Premium</label>
                </li>
            </ul>
            <aside>
                <img src="/icon/icons8-haute-importance-100.png" alt="Haute importance" width="25" height="25">
                <p>Attention! Une fois l'option choisi vous ne pourrez plus la modifier.</p>
            </aside>
            <?php } else { ?>
                <p>Comme vous êtres un professionnel public, l'offre crée sera gratuite (pas de facturation)</p>
                <p><a href="https://example.com">Plus d'informations&hellip;</a></p>
            <?php } ?>
        </section>

        <section id="info-generales">
            <h2>Informations générales</h2>
            <div>
                <label for="titre">Titre*</label>
                <p>
                    <input form="f" id="titre" name="titre" type="text" value="<?= htmlspecialchars($offre['titre']) ?>" required>
                </p>
                <label for="resume">Resumé*</label>
                <p>
                    <input form="f" id="resume" name="resume" type="text" value="<?= htmlspecialchars($offre['resume']) ?>" required>
                </p>
                <label for="adresse">Adresse*</label>
                <?php
                put_input_address('adresse', 'adresse_', 'f');
                $url = htmlspecialchars($offre['url_site_web'] ?? '');
                ?>
                <label for="site">Site Web</label>
                <p>
                    <input form="f" id="url_site_web" name="url_site_web" type="url" value="<?=$url ?>">
                </p>
            </div>
        </section>

        <section>
            <h2>Photo principale</h2>
            <input form="f" id="image_principale" name="image_principale" type="file" accept="image/*" required>
            <div id="image_principale-preview"></div>
        </section>
        <section id="tarifs">
            <h2>Tarifs</h2>
            <?php if ($est_prive) { ?>
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
                            <td><input type="text" placeholder="Enfant, Sénior&hellip;" required></td>
                            <td><input type="number" min="0" placeholder="Prix" required> €</td>
                        </tr>
                    </tfoot>
                </table>
                <template id="template-tarif-tr"><tr>
                    <td><input form="f" name="tarifs[nom][]" type="text" placeholder="Enfant, Sénior&hellip;" required readonly></td>
                    <td><input form="f" name="tarifs[montant][]" type="number" min="0" placeholder="Prix" required> €</td>
                </tr></template>
            <?php } else { ?>
                <p>En tant que professionnel public, vous ne pouvez pas ajouter de grillle tarifaire à votre offre gratuite.</p>
            <?php } ?>
        </section>

        <section id="horaires-hebdomadaires">
            <h2>Horaires hebdomadaires</h2>
            <div>
                <?php foreach (JOURS_SEMAINE as $dow => $jour) { ?>
                    <article id="<?= $jour ?>">
                        <h3><?= ucfirst($jour) ?></h3>
                        <button id="button-add-horaire-<?= $dow ?>" type="button">+</button>
                        <table id="table-horaires-<?= $dow ?>">
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
                    <template id="template-horaire-tr-<?= $dow ?>"><tr>
                        <td><input form="f" name="horaires[<?= $dow ?>][debut][]" type="time" required></td>
                        <td><input form="f" name="horaires[<?= $dow ?>][fin][]" type="time" required></td>
                        <td><button type="button">-</button></td>
                    </tr></template>
                <?php } ?>
            </div>
        </section>

        <section id="horaires-ponctuels">
            <h2>Horaires ponctuels</h2>
            <table id="table-periodes">
                <thead>
                    <tr>
                        <th>Début</th>
                        <th>Fin</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <td><input type="datetime-local" placeholder="Début" required></td>
                        <td><input type="datetime-local" placeholder="Fin" required></td>
                    </tr>
                </tfoot>
            </table>
            <template id="template-periode-tr"><tr>
                <td><input form="f" name="periodes[debut][]" type="datetime-local"></td>
                <td><input form="f" name="periodes[fin][]" type="datetime-local"></td>
            </tr></template>
        </section>

        <section id="tags">
            <h2>Tags</h2>
            <ul id="list-tag">
                <?php
                // Récupération des tags associés à l'offre
                $tags_remplis = DB\query_tags($offre['id']);
                $tags_disponibles = $args['type_offre'] === 'restaurant' ? TAGS_RESTAURANT : DEFAULT_TAGS;
                print_r($tags_remplis['tags']);
                foreach ($tags_disponibles as $tag) {
                    // Si le tag est déjà dans les tags remplis, ajoutez 'checked'
                    $checked = in_array($tag, $tags_remplis) ? 'checked' : '';
                    ?>
                    <li>
                        <label>
                            <input form="f" name="tags[<?= htmlspecialchars($tag) ?>]" type="checkbox" <?= $checked ?>>
                            <?= htmlspecialchars($tag) ?>
                        </label>
                    </li>
                <?php } ?>
            </ul>
        </section>

        <section id="description_detaillee">
            <label for="description_detaillee">
                <h2>Description détaillée</h2>
            </label>
            <textarea form="f" id="description_detaillee" name="description_detaillee" required><?= htmlspecialchars($offre['description_detaillee']) ?></textarea>
        </section>

        <section id="image-creation-offre">
            <label for="gallerie[]">
                <h2>Gallerie</h2>
            </label>
            <input form="f" id="gallerie" name="gallerie[]" type="file" accept="image/*" multiple>
            <div id="gallerie-preview"></div>
        </section>

        <section id="infos-detaillees">
            <h2>Informations détaillées</h2>
            <?php
            switch ($args['type_offre']) {
                case 'activité':
                    $info = DB\query_activite($offre['id']);
                    ?>
                    <p><label>Âge requis&nbsp;: <input form="f" name="age_requis" type="number" min="1" value="<?= htmlspecialchars($info['age_requis']) ?>"> an</label></p>
                    <p>Prestations incluses*</p>
                    <?php 
                        $prestations_incluses = htmlspecialchars($info['prestations_incluses'] ?? '');
                        $prestations_non_incluses = htmlspecialchars($info['prestations_non_incluses'] ?? '');
                    ?>
                    <textarea form="f" name="prestations_incluses" required><?= $prestations_incluses?></textarea>
                    <p>Prestations non incluses</p>
                    <textarea form="f" name="prestations_non_incluses"><?= $prestations_non_incluses?></textarea>
                    <?php
                    put_input_indication_duree(Duree::parse($info['indication_duree']));
                    break;
                case 'parc d\'attractions':
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
    <?php $page->put_footer() ?>
    
</body>

</html>
<?php
function put_input_indication_duree(Duree $duree)
{
    ?>
        <label>Durée estimée&nbsp;: <?php (new InputDuree('f', 'indication_duree', 'indication_duree_'))->put() ?></label>
        <?php
}
