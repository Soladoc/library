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
    <template id="template-tarif-row">
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
        <section id="image-creation-offre" style="background-color: gray;">
            <h2>Image</h2>
            <p style="background-color: gray;">À ajouter plus tard</p>
            <!-- Choisit 1 photo principale + 0 ou plus photos dans la gallerie -->
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
                <tbody id="tbody-tarifs">
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
                <article id="lundi">
                    <div>
                        <h3>lundi</h3>
                        <button type="button" onclick="ajoutHorraire('lundi')">
                            <img src="../icon/icons8-ajouter-90.png" alt="Ajouter Horraire">
                        </button>
                    </div>
                    <!-- ici se trouverons les horaires ajoutés en javaScript -->
                </article>
                <article id="mardi">
                    <div>
                        <h3>mardi</h3>
                        <button type="button" onclick="ajoutHorraire('mardi')">
                            <img src="../icon/icons8-ajouter-90.png" alt="Ajouter Horraire">
                        </button>
                    </div>
                </article>
                <article id="mercredi">
                    <div>
                        <h3>mercredi</h3>
                        <button type="button" onclick="ajoutHorraire('mercredi')">
                            <img src="../icon/icons8-ajouter-90.png" alt="Ajouter Horraire">
                        </button>
                    </div>
                </article>
                <article id="jeudi">
                    <div>
                        <h3>Jeudi</h3>
                        <button type="button" onclick="ajoutHorraire('jeudi')">
                            <img src="../icon/icons8-ajouter-90.png" alt="Ajouter Horraire">
                        </button>
                    </div>
                </article>
                <article id="vendredi">
                    <div>
                        <h3>Vendredi</h3>
                        <button type="button" onclick="ajoutHorraire('vendredi')">
                            <img src="../icon/icons8-ajouter-90.png" alt="Ajouter Horraire">
                        </button>
                    </div>
                </article>
                <article id="samedi">
                    <div>
                        <h3>Samedi</h3>
                        <button type="button" onclick="ajoutHorraire('samedi')">
                            <img src="../icon/icons8-ajouter-90.png" alt="Ajouter Horraire">
                        </button>
                    </div>
                </article>
                <article id="dimanche">
                    <div>
                        <h3>Dimanche</h3>
                        <button type="button" onclick="ajoutHorraire('dimanche')">
                            <img src="../icon/icons8-ajouter-90.png" alt="Ajouter Horraire">
                        </button>
                    </div>
                </article>
            </div>
        </section>
        <section id="info-localisation">
            <h2>Localisation</h2>
            <div>
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
        <section id="tags">
            <h2>Tags</h2>
            <ul id="list-tag">
                <?php
                    require_once 'tags.php';
                    foreach ($type_offre === 'restauration' ? TAGS_RESTAURATION : DEFAULT_TAGS as $id => $name) {
                ?><li><label for="tag-<?= $id ?>"><?= $name ?><input type="checkbox" name="tag-<?= $id ?>" id="tag-<?= $id ?>" vaelu></li></label><?php
                    }
                    ?>
            </ul>
        </section>
        <section id="description">
            <h2>Description</h2>
            <textarea name="description" id="description"></textarea>
        </section>
        <form id="form-offre" action="" method="post">
            <button type="submit">Valider</button>
        </form>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>