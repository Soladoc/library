<?php
require_once 'component/offre.php';
require_once 'component/Page.php';

$page = new Page('Accueil');


$recherche = null;

$valider = getarg($_GET,"valider",required: false);
$mot_cle= getarg($_GET,"mot_cle",required: false);
$modif_affichage = false;

if ($valider && !empty($mot_cle)) {
    $modif_affichage = true;
    $mot_cle= getarg( $_GET,"mot_cle");
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>
        <!-- Section de recherche -->
        <section class="search-section">
            <h1>Accueil</h1>
            <br>
            <form action="" name="bare_de_recherche" method="get" class="search-bar">

                <input type="text" name="mot_cle" value="<?php echo $mot_cle ?>" placeholder=">Mots-clés">
                <input type="submit" name="valider" value="Recherche">
            </form>
        </section>

        <!-- Section des offres à la une -->
        <section class="highlight-offers">
            <h2>Offres à la une</h2>
            <div class="offer-list">
                <?php

                if ($modif_affichage) {
                    $offres = DB\querry_select_offre_motcle($mot_cle);
                }
                else {
                    $offres = DB\query_offres_a_une();

                }
                // Préparer et exécuter la requête SQL pour récupérer toutes les offres

                // Boucler sur les résultats pour afficher chaque offre
                foreach ($offres as $offre) {
                    put_card_offre($offre);
                }
                ?>
            </div>
        </section>
    </main>
    <?php $page->put_footer() ?>
</body>

</html>
