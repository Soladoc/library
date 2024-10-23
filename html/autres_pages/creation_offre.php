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

<?php
const TYPE_OFFRE_AFFICHABLE = [
    'spectacle' => 'un spectacle',
    'parc-attraction' => "un parc d'attraction",
    'visite' => 'une visite',
    'restauration' => 'un restaurant',
    'activite' => 'une activité',
    '' => 'une offre'
];
?>

<body>
    <?php require 'component/header.php' ?>
    <template id="template-tarif-tr">
        <tr>
            <td></td>
            <td><input name="input-montant-tarif" type="number" min="0" placeholder="Prix" required> €</td>
            <td><button type="button">-</button></td>
        </tr>
    </template>
    <?php if (!isset($_GET) || !($type_offre = $_GET['type-offre'])) { ?>
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
                    <input type="text" name="titre" id="titre" maxlength="255" required>
                </p>
                <label for="resume">Resumé*</label>
                <p>
                    <input type="text" name="resume" id="resume" maxlength="1023" required>
                </p>
                <label for="adresse">Adresse*</label>
                <p>
                    <input type="text" name="adresse" id="adresse" readonly required>
                    <button type="button">&hellip;</button>
                </p>
                <label for="tel">Tel</label>
                <p>
                    <input type="tel" name="tel" id="tel">
                </p>
                <label for="site">Site Web</label>
                <p>
                    <input type="url" name="site" id="site">
                </p>
            </div>
        </section>
        <section>
            <h2>Photo principale</h2>
            <input type="file" id="photo-principale" name="photo-principale" accept="image/*" required>
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
                        <td><input id="nom-tarif" type="text" placeholder="Enfant, Sénior&hellip;"></td>
                        <td><input id="montant-tarif" type="number" min="0" placeholder="Prix"> €</td>
                        <td><button id="button-add-tarif" type="button">+</button></td>
                    </tr>
                </tfoot>
            </table>
        </section>
        <section id="horaires">
            <h2>Horaires</h2>
            <div>
                <?php
                function article_horaires(string $jour) { ?>
                    <template id="template-horaire-tr-<?= $jour ?>">
                        <tr>
                            <td><input type="time" name="heure-debut-<?= $jour ?>"></td>
                            <td><input type="time" name="heure-fin-<?= $jour ?>"></td>
                            <td><button type="button">-</button></td>
                        </tr>
                    </template>
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
                <?php }
                foreach (['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'] as $jour) {
                    article_horaires($jour);
                }
                ?>
            </div>
        </section>
        <section id="tags">
            <h2>Tags</h2>
            <ul id="list-tag">
                <?php
                require_once 'tags.php';
                foreach ($type_offre === 'restauration' ? TAGS_RESTAURATION : DEFAULT_TAGS as $id => $name) { ?>
                    <li><label for="tag-<?= $id ?>"><?= $name ?><input type="checkbox" name="tag-<?= $id ?>" id="tag-<?= $id ?>" vaelu></li></label>
                <?php } ?>
            </ul>
        </section>
        <section id="description">
            <h2>Description</h2>
            <textarea name="description" id="description"></textarea>
        </section>
        <section id="image-creation-offre">
            <h2>Gallerie</h2>
            <input type="file" id="gallerie" name="gallerie" accept="image/*" multiple>
            <ul id="gallerie-preview"></ul>
        </section>
        <form id="form-offre" action="" method="post">
            <button type="submit">Valider</button>
        </form>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>