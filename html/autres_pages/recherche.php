<?php
require_once 'component/offre.php';
require_once 'component/offre.php'
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche</title>
    <link rel="stylesheet" href="/style/style.css">
    <script src="/script_js/tri_recherche.js" defer></script>
</head>
<body>
    <?php require 'component/header.php' ?>
    <main>
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
                <div class="category-dropdown">
                    <select id="main-category" onchange="showSubcategories()">
                        <option value="">-- Sélectionnez une catégorie --</option>
                        <option value="restauration">Restauration</option>
                        <option value="activite">Activité</option>
                        <option value="visite">Visite</option>
                        <option value="spectacle">Spectacle</option>
                    </select>
                </div>
            </div>
            <div id="subcategories" class="hidden">
                <h3>Sous-catégories</h3>
                <div class="subcategory-list" id="subcategory-list"></div>
            </div>
        </section>
        <section class="sorting-section">
            <br>
            <h3>Options de tri</h3>
            <div class="sorting-buttons">
                <button id="sort-price-up" class="btn-sort" data-criteria="prix" data-order="asc">Prix ↑</button>
                <button id="sort-price-down" class="btn-sort" data-criteria="prix" data-order="desc">Prix ↓</button>
                <button id="sort-rating-up" class="btn-sort" data-criteria="note" data-order="asc">Note ↑</button>
                <button id="sort-rating-down" class="btn-sort" data-criteria="note" data-order="desc">Note ↓</button>
                <button id="sort-date-up" class="btn-sort" data-criteria="date" data-order="asc">Date ↑</button>
                <button id="sort-date-down" class="btn-sort" data-criteria="date" data-order="desc">Date ↓</button>
            </div>
        </section>
        <section class="highlight-offers">
            <h2>Offres trouvées :</h2>
            <div class="offer-list">
                
            </div>
        </section>
    </main>
    <?php require 'component/footer.php' ?>
    <script src="/script_js/tri_recherche.js"></script>
    <script>
        <?php
        $stmtOffres = query_offres();
        $offres = $stmtOffres->fetchAll(PDO::FETCH_ASSOC);
        foreach ($offers as &$offre) {
            $offre['formatted_address'] = format_adresse(notfalse(query_adresse($offre['id_adresse'])));
        }
        echo "const offersData = " . json_encode($offres) . ";";
        ?>
        document.addEventListener('DOMContentLoaded', function() {
            initializeOffers(offersData);
        });
    </script>
</body>
</html>
