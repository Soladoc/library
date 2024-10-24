<?php
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
require_once 'db.php';

if ($type_offre && $_POST) {
    $pdo    = db_connect();
    $sql    = 'INSERT INTO pact._offre ( titre, resume, description_detaille, url_site_web, adresse) VALUES ( :titre, :resume, :description_detaille, :url_site_web, :adresse)';
    $stmt   = $pdo->prepare($sql);

    $stmt->bindParam(':titre',$_POST['titre']);
    $stmt->bindParam(':resume',$_POST['resume']);
    $stmt->bindParam(':description_detaille',$_POST['description']);
    $stmt->bindParam(':url_site_web',$_POST['site']);
    $stmt->bindParam(':adresse',$_POST['adresse']);

    $stmt->execute([
        ':titre' => $_POST['titre'],
        ':resume' => $_POST['resume'],
        ':description_detaille' => $_POST['description'],
        ':url_site_web' => $_POST['site'],
        ':adresse' => $_POST['adresse']
    ]);

    ?>
    <pre><?php htmlspecialchars(print_r($_GET)) ?></pre>
    <pre><?php htmlspecialchars(print_r($_POST)) ?></pre>
    <pre><?php htmlspecialchars(print_r($_FILES)) ?></pre>

    
<?php
} else {
?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <script async src="../script_js/creation_offre.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../style/style.css">
        <link rel="stylesheet" href="../style/creation_offre.css">
        <title>Création d'une offre</title>
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
                        <input form="form-offre" type="tel" name="tel" id="tel">
                    </p>
                    <label for="site">Site Web</label>
                    <p>
                        <input form="form-offre" type="url" name="site" id="site">
                    </p>
                </div>
            </section>
            <section>
                <h2>Photo principale</h2>
                <input form="form-offre" type="file" id="photo-principale" name="photo-principale" accept="image/*" required>
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
                                <td><input form="form-offre" type="time" name="horaires_deb[<?= $jour ?>]" required></td>
                                <td><input form="form-offre" type="time" name="horaires_fin[<?= $jour ?>]" required></td>
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
                <textarea name="description" id="description"></textarea>
            </section>
            <section id="image-creation-offre">
                <h2>Gallerie</h2>
                <input form="form-offre" type="file" id="gallerie" name="gallerie" accept="image/*" multiple>
                <ul id="gallerie-preview"></ul>
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
                        <textarea name="<?= $type_offre ?>[carte]"></textarea>
                        <?php
                        break;
                    case 'activite':
                        ?>
                        <p><label>Durée indiquée&nbsp;: <input name="<?= $type_offre ?>[indication_duree]" type="time" required></label></p>
                        <p><label>Âge requis&nbsp;: <input name="<?= $type_offre ?>[age_requis]" type="number" min="1"> an</label></p>
                        <p>Prestations incluses</p>
                        <textarea name="<?= $type_offre ?>[prestations_incluses]"></textarea>
                        <p>Prestations non incluses</p>
                        <textarea name="<?= $type_offre ?>[prestations_non_incluses]"></textarea>
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