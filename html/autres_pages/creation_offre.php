<?php
session_start();

require_once 'db.php';
require_once 'connexion.php';

// $id_pro = exiger_connecte_pro();
$id_pro = 1;

const TYPE_OFFRE_AFFICHABLE = [
    'spectacle' => 'un spectacle',
    'parc-attraction' => "un parc d'attraction",
    'visite' => 'une visite',
    'restauration' => 'un restaurant',
    'activite' => 'une activité',
    '' => 'une offre'
];

const JOURS_SEMAINE = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

$type_offre = $_GET['type-offre'] ?? null;

// Conserve les images uploadées durant cette transaction pour les supprimer en cas d'erreur. Comme ça on ne pollue pas le dossier.
$uploaded_images = [];

function remove_uploaded_images()
{
    global $uploaded_images;
    foreach ($uploaded_images as $file) {
        unlink($file);
    }
}

function insert_image(PDO $pdo, array $img)
{
    global $uploaded_images;
    $stmt = notfalse($pdo->prepare('insert into pact._image (legende, taille) values (?,?) returning id_image'));
    notfalse($stmt->execute(['', $img['size']]));  // todo: legende
    $id_image = $stmt->fetchColumn();

    move_uploaded_file($img['tmp_name'], __DIR__ . "/../images_utilisateur/$id_image");
}

if ($type_offre && $_POST) {
    ?>
<pre><?= htmlspecialchars(print_r($_GET, true)) ?></pre>
<pre><?= htmlspecialchars(print_r($_POST, true)) ?></pre>
<pre><?= htmlspecialchars(print_r($_FILES, true)) ?></pre>
<?php

    try {
        $pdo = db_connect();
        notfalse($pdo->beginTransaction());

        // Récupérer le code de la commune
        // todo: il faut un code postal avec (car une commune peut avoir plusieurs codes postaux)
        $stmt = notfalse($pdo->prepare('select code_insee, code_postal from pact._commune where nom=?'));
        echo 'fetching commune ' . $_POST['adresse']['commune'];
        notfalse($stmt->execute([$_POST['adresse']['commune']]));
        $commune = notfalse($stmt->fetch(PDO::FETCH_ASSOC));

        // Insérer l'adresse
        // todo: adresses localisées
        $stmt = notfalse($pdo->prepare('insert into pact._adresse (numero_voie, complement_numero, nom_voie, localite, precision_int, precision_ext, commune_code_insee, commune_code_postal) values (?,?,?,?,?,?,?,?) returning id_adresse'));
        notfalse($stmt->execute([
            $_POST['adresse']['num_voie'] ?? 0,
            $_POST['adresse']['compl_numero'] ?? '',
            $_POST['adresse']['nom_voie'] ?? '',
            $_POST['adresse']['localite'] ?? '',
            $_POST['adresse']['precision_int'] ?? '',
            $_POST['adresse']['precision_ext'] ?? '',
            $commune['code_insee'],
            $commune['code_postal']
        ]));
        $id_adresse = notfalse($stmt->fetchColumn());

        // Insérer la gallerie la photo principale
        $id_image_photo_principale = insert_image($pdo, $_FILES['photo_principale']);

        // Insérer le signalable
        $stmt = notfalse($pdo->prepare('insert into pact._signalable default values returning id_signalable'));
        notfalse($stmt->execute());
        $id_signalable = notfalse($stmt->fetchColumn());

        // Insérer l'offre
        // todo: offre payantes standard et premium

        $stmt = notfalse($pdo->prepare('INSERT INTO pact._offre (titre, resume, description_detaille, url_site_web, adresse, photoprincipale, abonnement, id_signalable, id_professionnel) VALUES (?,?,?,?,?,?,?,?,?) returning id_offre'));
        notfalse($stmt->execute([
            $_POST['titre'],
            $_POST['resume'],
            $_POST['description'] ?? '',
            $_POST['site'] ?? '',
            $id_adresse,
            $id_image_photo_principale,
            'gratuit',
            $id_signalable,
            $id_pro
        ]));
        $id_offre = notfalse($stmt->fetchColumn());

        // Insérer la gallerie
        $gallerie = [];
        // convert from SOA (structure of arrays) to AOS (array of structures)
        foreach ($_FILES['gallerie'] as $key => $all) {
            foreach ($all as $i => $val) {
                $gallerie[$i][$key] = $val;
            }
        }

        foreach ($gallerie as $img) {
            $id_image = insert_image($pdo, $img);
            $stmt = notfalse($pdo->prepare('insert into pact._gallerie (id_offre, id_image) values (?,?)'));
            notfalse($stmt->execute([$id_offre, $id_image]));
        }

        $pdo->commit();
        echo "j'ai réussi";
    } catch (Throwable $e) {
        remove_uploaded_images();
        notfalse($pdo->rollBack());
        echo "j'ai échoué";
        throw $e;
    }
} else {
    ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
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
                        put_input_address('form-offre');
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
            <input form="form-offre" type="file" id="photo-principale" name="photo_principale" accept="image/*" required>
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
                    foreach ($type_offre === 'restauration' ? TAGS_RESTAURATION : DEFAULT_TAGS as $id => $name) {
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
                    case 'parc-attraction':
                        ?>
            <p><label>Âge requis&nbsp;: <input name="<?= $type_offre ?>[age_requis]" type="number" min="1"> an</label></p>
            <?php
                        break;
                    case 'visite':
                        ?>
            <p><label>Durée indiquée&nbsp;: <input name="<?= $type_offre ?>[indication_duree]" type="time" required></label></p>
            <?php
                        break;
                    case 'restauration':
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
        <!-- du coup en sois on a pas de formulaire a check avec Raphael -->
        <form id="form-offre" action="creation_offre.php?type-offre=<?= $type_offre ?>" method="post" enctype="multipart/form-data">
            <button type="submit">Valider</button>
        </form>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>
<?php } ?>