<!DOCTYPE html>
<html lang="fr">

<head>
    <script async src="../script_js/creation_offre.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Création d'une offre</title>
</head>

<?php
const TYPE_OFFRE_AFFICHABLE = [
    'spectacle' => 'un spectacle',
    'parc_attraction' => "un parc d'attraction",
    'visite' => 'une visite',
    'restauration' => 'une restauration',
    'activite' => 'une activité',
    '' => 'une offre'
];
?>

<body>
    <template id="template_tarif_row">
        <tr>
            <td></td>
            <td><input name="montant_tarif" type="number" placeholder="montant" required> €</td>
            <td><button type="button">-</button></td>
        </tr>
    </template>
    <?php require 'header.php' ?>
    <main>
        <section id="titre_creation_offre">
            <?php if (isset($_GET)) { ?>
            <h1>Créer <?= TYPE_OFFRE_AFFICHABLE[$_GET['type_offre'] ?? ''] ?></h1>
            <?php } else { ?>
            <!-- post is unset-->
            <h1>Erreur de méthode d'accès</h1>
            <p>Une erreur dans la requette de la page est survenue, merci de réessayer plus tard</p>
            <?php } ?>
        </section>
        <section id="image_creation_offre" style="background-color: gray;">
            <p style="background-color: gray;">À ajouter plus tard</p>
            <!-- Choisit 1 photo principale + 0 ou plus photos dans la gallerie -->
        </section>
        <form action="" method="post">
            <section id="tarif">
                <article>
                    <h3>Tarifs</h3>
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
                </article>
                <article id="tarif_ajoute">

                </article>
            </section>
            <section id="horaires">
                <article>
                    <h2>Horaires</h2>
                </article>
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
                <h3>Info/Localisation</h3>

                <label for="adresse">Adresse*</label>
                <input type="text" name="adresse" id="adresse" required>

                <label for="tel">Tel</label>
                <input type="text" name="tel" id="tel">

                <label for="site">Site Web</label>
                <input type="text" name="site" id="site">

            </section>
            <section id="description">
                <label for="description">Description</label>
                <textarea name="description" id="description"></textarea>
            </section>
            <button type="submit">Valider</button>
        </form>
    </main>
    <?php require 'footer.php' ?>
</body>

</html>