<?php
// // Vérifier si l'ID est présent dans l'URL
// if (isset($_GET['id']) && is_numeric($_GET['id'])) {
//     $id = $_GET['id'];

//     // Connexion à la base de données
//     $pdo = db_connect();

//     // Préparer la requête pour récupérer les détails de l'offre
//     $query = "SELECT * FROM offres WHERE id = :id";
//     $stmt = $pdo->prepare($query);
//     $stmt->execute(['id' => $id]);

//     // Récupérer les données de l'offre
//     $offre = $stmt->fetch();

//     // Si l'offre est trouvée, afficher ses détails
//     if ($offre) {
//         echo '<h3>' . htmlspecialchars($offre['title']) . '</h3>';
//         echo '<p class="location">' . htmlspecialchars($offre['location']) . '</p>';
//         echo '<p class="category">Catégorie&nbsp;: ' . htmlspecialchars($offre['category']) . '</p>';
//         echo '<p class="price">Prix&nbsp;: ' . htmlspecialchars($offre['price']) . '</p>';
//         echo '<p class="rating">Note&nbsp;: ' . htmlspecialchars($offre['rating']) . ' ★ (' . htmlspecialchars($offre['reviews']) . ' avis)</p>';
//         echo '<p class="professional">Proposé par&nbsp;: ' . htmlspecialchars($offre['professional']) . '</p>';
//     } else {
//         echo 'Aucune offre trouvée avec cet ID.';
//     }
// } else {
//     echo 'ID d\'offre invalide.';
// }

const TYPE_OFFRE_AFFICHABLE = [
    'spectacle' => 'un spectacle',
    'parc_attraction' => "un parc d'attractions",
    'visite' => 'une visite',
    'restauration' => 'une restauration',
    'activite' => 'une activité',
    '' => 'une offre'
];

?>




<!DOCTYPE html>
<html lang="fr">

<head>
    <script async src="../script_js/creation_offre.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style.css">
    <link rel="stylesheet" href="../style/creation_offre.css">
    <title>Création d'une offre</title>
</head>

<body>
    <?php require 'component/header.php' ?>
    <template id="template_tarif_row">
        <tr>
            <td></td>
            <td><input name="montant_tarif" type="number" placeholder="montant" required> €</td>
            <td><button type="button">-</button></td>
        </tr>
    </template>
    <?php if (!isset($_GET) || !($type_offre = $_GET['type_offre'])) { ?>
    <!-- post is unset-->
    <h1>Erreur de méthode d'accès</h1>
    <p>Une erreur dans la requette de la page est survenue, merci de réessayer plus tard</p>

    <?php exit; } ?>
    <main>
        <section id="titre_creation_offre">
            <h1>Créer <?= TYPE_OFFRE_AFFICHABLE[$type_offre] ?></h1>
        </section>
        <section id="image_creation_offre" style="background-color: gray;">
            <h2>Image</h2>
            <p style="background-color: gray;">À ajouter plus tard</p>
            <!-- Choisit 1 photo principale + 0 ou plus photos dans la gallerie -->
        </section>
        <form action="" method="post">
            <section id="tarif">
                <h2>Tarifs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Dénomination</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_tarifs">

                    </tbody>
                    <tfoot>
                        <tr>
                            <td><input id="nom_tarif" type="text" placeholder="Nom"></td>
                            <td><input id="montant_tarif" type="number" min="0" placeholder="Montant"> €</td>
                            <td><button id="button_add_tarif" type="button">+</button></td>
                        </tr>
                        </tbody>
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
            <section id="info_localisation">
                <h2>Info / Localisation</h2>
                <p>
                    <label for="adresse">Adresse*</label>
                    <input type="text" name="adresse" id="adresse" required>
                    <button type="button">&hellip;</button>
                </p>
                <p>
                    <label for="tel">Tel</label>
                    <input type="text" name="tel" id="tel">
                </p>
                <p>
                    <label for="site">Site Web</label>
                    <input type="text" name="site" id="site">
                </p>
            </section>
            <section id="tags">
                <h2>Tags</h2>
                <ul>
                <?php
                require_once 'tags.php';
                foreach ($type_offre === 'restauration' ? TAGS_RESTAURANT : DEFAULT_TAGS as $tag) {
                    ?><li><?= $tag ?></li><?php
                }
                ?>
                </ul>
            </section>
            <section id="description">
                <h2>Description</h2>
                <textarea name="description" id="description"></textarea>
            </section>
            <button type="submit">Valider</button>
        </form>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>