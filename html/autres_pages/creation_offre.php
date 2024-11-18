<?php
session_start();

require_once 'queries.php';
require_once 'auth.php';
require_once 'util.php';

// $ID_PRO = exiger_connecte_pro();
$ID_PRO = 1;

const TYPE_OFFRE_AFFICHABLE = [
    'spectacle' => 'un spectacle',
    'parc-attractions' => "un parc d'attractions",
    'visite' => 'une visite',
    'restaurant' => 'un restaurant',
    'activite' => 'une activité',
    '' => 'une offre'
];

const JOURS_SEMAINE = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

$type_offre = $_GET['type-offre'] ?? null;

if ($type_offre && $_POST) {
    /*?>
    <pre><?= htmlspecialchars(print_r($_GET, true)) ?></pre>
    <pre><?= htmlspecialchars(print_r($_POST, true)) ?></pre>
    <pre><?= htmlspecialchars(print_r($_FILES, true)) ?></pre>
    <?php*/
    // Conserve les images uploadées durant cette transaction pour les supprimer en cas d'erreur. Comme ça on ne pollue pas le dossier.
    $uploaded_files = [];
    transaction(function () {
        global $ID_PRO;
        $pdo = db_connect();
        notfalse($pdo->beginTransaction());

        // Récupérer le code de la commune
        // todo: make this better (by inputting either nom or code postal)
        $commune = single(query_communes($_POST['adresse']['commune']));

        // Insérer l'adresse
        // todo: adresses localisées
        $id_adresse = insert_adresse(
            $commune['code'],
            $commune['numero_departement'],
            $_POST['adresse']['numero-voie'] ?? null,
            $_POST['adresse']['complement-numero'] ?? null,
            $_POST['adresse']['nom-voie'] ?? null,
            $_POST['adresse']['localite'] ?? null,
            $_POST['adresse']['precision-int'] ?? null,
            $_POST['adresse']['precision-ext'] ?? null,
        );

        // Insérer la gallerie la photo principale
        [$uploaded_files[], $id_image_photo_principale] = insert_image($_FILES['id-image-principale']);

        // Insérer l'offre
        $offre_args = offre_args(
            $id_adresse,
            $id_image_photo_principale,
            $ID_PRO,
            'gratuit', // todo: standard et premium
            $_POST['titre'],
            $_POST['resume'],
            $_POST['description'],
            $_POST['site'] ?? null
        );

        global $type_offre;
        switch ($type_offre) {
            case 'spectacle':
                $id_offre = insert_spectacle(
                    $offre_args,
                    $_POST['indication-duree'],
                    $_POST['capacite-accueil'],
                );
                break;
            case 'parc-attractions':
                $id_offre = insert_parc_attractions(
                    $offre_args,
                    $_POST['id-image-plan'],
                );
                break;
            case 'visite':
                $id_offre = insert_visite(
                    $offre_args,
                    $_POST['indication-duree'],
                );
                break;
            case 'restaurant':
                $id_offre = insert_restaurant(
                    $offre_args,
                    $_POST['carte'],
                    $_POST['richesse'],
                    $_POST['sert-petit-dejeuner'] ?? null,
                    $_POST['sert-brunch'] ?? null,
                    $_POST['sert-dejeuner'] ?? null,
                    $_POST['sert_diner'] ?? null,
                    $_POST['sert_boissons'] ?? null,
                );
                break;
            case 'activite':
                $id_offre = insert_activite(
                    $offre_args,
                    $_POST['indication-duree'],
                    $_POST['prestations-incluses'],
                    $_POST['age-requis'] ?? null,
                    $_POST['prestations-non-incluses'] ?? null,
                );
                break;
        }

        // Insérer la gallerie
        $gallerie = [];
        // convert from SOA (structure of arrays) to AOS (array of structures)
        foreach ($_FILES['gallerie'] as $key => $all) {
            foreach ($all as $i => $val) {
                $gallerie[$i][$key] = $val;
            }
        }

        foreach ($gallerie as $img) {
            [$uploaded_files[], $id_image] = insert_image($img);
            $stmt = notfalse($pdo->prepare('insert into pact._gallerie (id_offre, id_image) values (?,?)'));
            notfalse($stmt->execute([$id_offre, $id_image]));
        }

        $pdo->commit();
    }, function () {
        global $uploaded_files;
        foreach ($uploaded_files as $file) {
            unlink($file);
        }
    });
} else {
    ?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/style/style.css">
        <link rel="stylesheet" href="../style/creation_offre.css">
        <title>Création d'une offre</title>
        <script src="../script_js/creation_offre.js"></script>
    </head>

    <body>
        <?php require 'component/header.php' ?>
        <?php if (!isset($_GET) || !$type_offre) { ?>
            <!-- post is unset-->
            <h1>Erreur de méthode d'accès</h1>
            <p>Une erreur dans la requette de la page est survenue, merci de réessayer plus tard</p>

            <?php exit;
        } ?>
        <main>
            <section id="titre-creation-offre">
                <h1>Créer <?= TYPE_OFFRE_AFFICHABLE[$type_offre] ?></h1>
            </section>
            <section id="info-generales">
                <h2>Informations générales</h2>
                <div>
                    <label for="titre">Titre*</label>
                    <p>
                        <input form="form-offre" type="text" name="titre" id="titre" maxlength="255" required>
                    </p>
                    <label for="resume">Resumé*</label>
                    <p>
                        <input form="form-offre" type="text" name="resume" id="resume" maxlength="1023" required>
                    </p>
                    <label for="adresse">Adresse*</label>
                    <p>
                        <?php
                        require_once 'component/input_address.php';
                        put_input_address('form-offre')
                            ?>
                    </p>
                    <label for="tel">Tel</label>
                    <p>
                        <input form="form-offre" type="tel" name="tel" id="tel" pattern="0\d{9}" title="10 chiffres (France uniquement)" placeholder="0123456789">
                    </p>
                    <label for="site">Site Web</label>
                    <p>
                        <input form="form-offre" type="url" name="site" id="site">
                    </p>
                </div>
            </section>
            <section>
                <h2>Photo principale</h2>
                <input form="form-offre" type="file" id="photo-principale" name="id-image-principale" accept="image/*" required>
                <div id="photo-principale-preview"></div>
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
                            <td><input id="nom-tarif" type="text" minlength="1" placeholder="Enfant, Sénior&hellip;"></td>
                            <td><input id="montant-tarif" type="number" min="0" placeholder="Prix"> €</td>
                            <td><button id="button-add-tarif" type="button">+</button></td>
                        </tr>
                    </tfoot>
                </table>
                <template id="template-tarif-tr">
                    <tr>
                        <td><input form="form-offre" name="tarifs[]" minlength="1" type="text" placeholder="Enfant, Sénior&hellip;" required readonly></td>
                        <td><input form="form-offre" name="tarifs_montant[]" min="0" type="number" placeholder="Prix" required> €</td>
                        <td><button type="button">-</button></td>
                    </tr>
                </template>
            </section>
            <section id="horaires">
                <h2>Horaires</h2>
                <div>
                    <?php
                    foreach (JOURS_SEMAINE as $jour) {
                        ?>
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
                        <template id="template-horaire-tr-<?= $jour ?>">
                            <tr>
                                <td><input form="form-offre" type="time" name="horaires_deb[<?= $jour ?>][]" required></td>
                                <td><input form="form-offre" type="time" name="horaires_fin[<?= $jour ?>][]" required></td>
                                <td><button type="button">-</button></td>
                            </tr>
                        </template>
                    <?php } ?>
                </div>
            </section>
            <section id="tags">
                <h2>Tags</h2>
                <ul id="list-tag">
                    <?php
                    require_once 'tags.php';
                    foreach ($type_offre === 'restaurant' ? TAGS_RESTAURANT : DEFAULT_TAGS as $id => $name) {
                        ?>
                        <li><label for="tag-<?= $id ?>"><?= $name ?><input form="form-offre" type="checkbox" name="tags[<?= $id ?>]" id="tag-<?= $id ?>" value="<?= $id ?>"></li></label>
                    <?php } ?>
                </ul>
            </section>
            <section id="description">
                <h2>Description</h2>
                <textarea form="form-offre" name="description" id="description" minlength="1" required></textarea>
            </section>
            <section id="image-creation-offre">
                <h2>Gallerie</h2>
                <input form="form-offre" type="file" id="gallerie" name="gallerie[]" accept="image/*" multiple>
                <div id="gallerie-preview"></div>
            </section>
            <section id="infos-detaillees">
                <h2>Informations détailées</h2>
                <?php
                switch ($type_offre) {
                    case 'spectacle':
                        ?>
                        <p><label>Durée indiquée&nbsp;: <input name="<?= $type_offre ?>[indication_duree]" type="time" required></label></p>
                        <p><label>Capacité d'accueil&nbsp;: <input name="<?= $type_offre ?>[capacite_accueil]" type="number" min="0"></label> pers.</p>
                        <?php
                        break;
                    case 'parc-attractions':
                        ?>
                        <p><label>Âge requis&nbsp;: <input name="<?= $type_offre ?>[age_requis]" type="number" min="1"> an</label></p>
                        <?php
                        break;
                    case 'visite':
                        ?>
                        <p><label>Durée indiquée&nbsp;: <input name="<?= $type_offre ?>[indication_duree]" type="time" required></label></p>
                        <?php
                        break;
                    case 'restaurant':
                        ?>
                        <fieldset>
                            <legend>Niveau de richesse</legend>
                            <p><label><input type="radio" name="<?= $type_offre ?>[richesse]" value="1" checked> €</label></p>
                            <p><label><input type="radio" name="<?= $type_offre ?>[richesse]" value="2"> €€</label></p>
                            <p><label><input type="radio" name="<?= $type_offre ?>[richesse]" value="3"> €€€</label></p>
                        </fieldset>
                        <fieldset>
                            <legend>Repas</legend>
                            <p><label><input type="checkbox" name="<?= $type_offre ?>[sert][petit_dejeuner]"> Petit déjeuner</label></p>
                            <p><label><input type="checkbox" name="<?= $type_offre ?>[sert][brunch]"> Brunch</label></p>
                            <p><label><input type="checkbox" name="<?= $type_offre ?>[sert][dejeuner]"> Déjeuner</label></p>
                            <p><label><input type="checkbox" name="<?= $type_offre ?>[sert][diner]"> Dîner</label></p>
                            <p><label><input type="checkbox" name="<?= $type_offre ?>[sert][boissons]"> Boissons</label></p>
                        </fieldset>
                        <p>Carte</p>
                        <textarea form="form-offre" name="<?= $type_offre ?>[carte]"></textarea>
                        <?php
                        break;
                    case 'activite':
                        ?>
                        <p><label>Durée indiquée&nbsp;: <input name="<?= $type_offre ?>[indication_duree]" type="time" required></label></p>
                        <p><label>Âge requis&nbsp;: <input name="<?= $type_offre ?>[age_requis]" type="number" min="1"> an</label></p>
                        <p>Prestations incluses</p>
                        <textarea form="form-offre" name="<?= $type_offre ?>[prestations_incluses]"></textarea>
                        <p>Prestations non incluses</p>
                        <textarea form="form-offre" name="<?= $type_offre ?>[prestations_non_incluses]"></textarea>
                        <?php
                        break;
                }
                ?>
            </section>
            <section id="infos-categorie">
                <!-- Utiliser des sections cachées et input disabled?  -->
            </section>
            <!-- du coup en sois on a pas de formulaire a check avec Raphael -->
            <form id="form-offre" action="creation_offre.php?type-offre=<?= $type_offre ?>" method="post" enctype="multipart/form-data">
                <button type="submit">Valider</button>
            </form>
        </main>
        <?php require 'component/footer.php' ?>
    </body>

    </html>
<?php } ?>