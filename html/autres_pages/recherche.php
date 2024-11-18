<?php
require_once 'component/offre.php';
require_once 'component/head.php';
?>

<!DOCTYPE html>
<html lang="fr">

<?php put_head('Recherche', scripts: ['tri_recherche.js' => 'defer']) ?>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <!-- Section de recherche -->
        <section class="search-section">
            <h1>Recherche</h1>
            <br>
            <div class="search-bar">
                <input type="text" placeholder="Rechercher des activités, restaurants, spectacles...">
                <a href="">
                    <button class="btn-search">Rechercher</button>
                </a>
            </div>
        </section>

        <section class="tag-selection">
            <div class="categories">
                <h3>Catégories</h3>
                <select id="main-category" onchange="showSubcategories()">
                    <option value="">-- Sélectionnez une catégorie --</option>
                    <option value="restauration">Restauration</option>
                    <option value="activite">Activité</option>
                    <option value="visite">Visite</option>
                    <option value="spectacle">Spectacle</option>
                </select>
            </div>

            <div id="subcategories" class="hidden">
                <h3>Sous-catégories</h3>
                <div class="subcategory-list" id="subcategory-list">
                </div>
            </div>
        </section>

        <section class="sorting-section">
            <br>
            <h3>Options de tri</h3>
            <div class="sorting-buttons">
                <button class="btn-sort" id="sort-price-up">Prix 🔼</button>
                <button class="btn-sort" id="sort-price-down">Prix 🔽</button>
                <button class="btn-sort" id="sort-rating-up">Notes 🔼</button>
                <button class="btn-sort" id="sort-rating-down">Notes 🔽</button>
                <button class="btn-sort" id="sort-date-up">Date de publication 🔼</button>
                <button class="btn-sort" id="sort-date-down">Date de publication 🔽</button>
            </div>
        </section>

        <section class="highlight-offers">
            <h2>Offres à la une</h2>
            <div class="offer-list">
                <?php
                    $stmtOffres = query_offres();

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
