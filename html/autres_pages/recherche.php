<?php
require_once 'component/Page.php';

$page = new Page('Recherche', scripts: [
    'tri_recherche.js' => 'defer',
]);


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
<script src="script_js\tri_recherche.js"></script>
    <?php $page->put_header() ?>
    <main>
        <section class="search-section">
            <h1>Recherche</h1>
            <br>
            <div class="search-bar">
                <!-- <input id="barre-recherche" type="text" placeholder="Rechercher des activités, restaurants, spectacles..."> -->
                <input type="text" id="keyword-search" placeholder="Rechercher par mot-clé" oninput="filterOffers()">
                
            </div>
        </section>

        <section class="tag-selection">
            <div class="categories">
                <h3>Catégories</h3>
                <div class="category-dropdown">
                    <select id="main-category" onchange="showSubcategories()">
                        <option value="">-- Sélectionnez une catégorie --</option>
                        <option value="restaurant">Restauration</option>
                        <option value="activité">Activité</option>
                        <option value="visite">Visite</option>
                        <option value="spectacle">Spectacle</option>
                        <option value="parc_d_attractions">Parc d'attractions</option>
                    </select>
                </div>
            </div>
            <input type="hidden" id="selected-category" name="category" value="">
            <div id="subcategories" class="hidden">
                <h3>Tags</h3>
                <div class="subcategory-list" id="subcategory-list">

                </div>
            </div>
        </section>
        <section class="sorting-section">
            <br>
            <h3>Options de tri</h3>
            <div class="sorting-buttons">
                <button id="sort-price-up" class="btn-sort" data-criteria="prix" data-order="asc">Prix croissant</button>
                <button id="sort-price-down" class="btn-sort" data-criteria="prix" data-order="desc">Prix décroissant</button>
                <button id="sort-rating-down" class="btn-sort" data-criteria="note" data-order="desc">Note croissante</button>
                <button id="sort-rating-up" class="btn-sort" data-criteria="note" data-order="asc">Note décroissante</button>
                <button id="sort-date-up" class="btn-sort" data-criteria="date" data-order="asc">Plus récent</button>
                <button id="sort-date-down" class="btn-sort" data-criteria="date" data-order="desc">Moins récent</button>
            </div>
        </section>
        <section class="highlight-offers">
            <h2>Offres trouvées :</h2>
            <div class="offer-list">
                
            </div>
        </section>
    </main>
    <?php $page->put_footer() ?>
</body>
</html>
