<!DOCTYPE html>
<html lang="fr">

<head>
    <script src="../script_js/creation_offre.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Création d'une offre</title>
</head>

<body>
    <?php require 'header.php' ?>
    <main>
        <section id="titre_creation_offre">
            <?php if (isset($_POST)) { ?>
            <?php

            /*
             * afin d'afficher le tire correctement j'effectu un switch sur le type d'offre
             * je ne fais pas <h1>Créer <?php echo ucfirst(str_replace('_',' ',$_POST['type_offre'])) ; \⸮><h1>
             * car il faut gérer le préfixe "un/une" qui nécéssite de toute facons un switch
             */
            $type_offre_affichable = '';
            switch ($_POST['type_offre']) {
                case 'spectacle':
                    $type_offre_affichable = 'un spectacle';
                    break;
                case 'parc_attraction':
                    $type_offre_affichable = "un parc d'attraction";
                    break;
                case 'visite':
                    $type_offre_affichable = 'une visite';
                    break;
                case 'restauration':
                    $type_offre_affichable = 'une restauration';
                    break;
                case 'activite':
                    $type_offre_affichable = 'une activité';
                    break;
                default:
                    $type_offre_affichable = 'une offre';
                    break;
            }
            ?>
            <h1>Créer <?php echo $type_offre_affichable; ?></h1>
            <?php } else { ?>
            <!-- post is unset-->
            <h1>Erreur de methode d'acces</h1>
            <p>Une erreur dans la requette de la page est survenu merci de réessayer plus tard</p>
            <?php } ?>
        </section>
        <section id="image_creation_offre" style="background-color: gray;">
            <p style="background-color: gray;">À ajouter plus tard</p>
        </section>
        <form action="" method="post">

            <section id="tarif">
                <article>
                    <h3>Tarifs</h3>
                    <!-- ajouter la taille dans le css -->
                    <button type="button" onclick="ajoutTarif();">
                        <img src="../icon/icons8-ajouter-90.png" alt="Ajouter tarif">
                    </button>
                </article>
                <article id="tarif_ajoute">

                </article>
            </section>
            <section id="horraires">
                <article>
                    <h2>horraires</h2>
                </article>
                <div>
                    <article id="lundi">
                        <div>
                            <h3>lundi</h3>
                            <button type="button" onclick="ajoutHorraire('lundi')">
                                <img src="../icon/icons8-ajouter-90.png" alt="Ajouter Horraire">
                            </button>
                        </div>
                        <!-- ici se trouverons les horraires ajoutés en javaScript -->
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
    <?php include ('footer.php'); ?>
</body>

</html>