<?php
require_once 'component/offre.php';
require_once 'component/head.php';
?>

<!DOCTYPE html>
<html lang="fr">

<?php put_head('Accueil') ?>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <!-- Section de recherche -->
        <section class="search-section">
            <h1>Accueil</h1>
            <br>
            <div class="search-bar">
                <input type="text" placeholder="Rechercher des activités, restaurants, spectacles...">
                <a href="recherche.php">
                    <button class="btn-search">Rechercher</button>
                </a>
            </div>
        </section>

        <!-- Section des offres à la une -->
        <section class="highlight-offers">
            <h2>Offres à la une</h2>
            <div class="offer-list">
                <?php
                    // Préparer et exécuter la requête SQL pour récupérer toutes les offres
                    $stmtOffres = query_offre_a_une();
                    
                    // Boucler sur les résultats pour afficher chaque offre
                    while ($offre = $stmtOffres->fetch()) {
                        put_card_offre($offre);
                    }
                ?>
            </div>
        </section>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>
