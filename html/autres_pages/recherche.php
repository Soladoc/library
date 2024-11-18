<?php
require_once 'component/offre.php'
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche</title>
    <link rel="stylesheet" href="/style/style.css">
</head>

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
            <h3>Options de tri</h3>
            <div class="sorting-buttons">
                <button class="btn-sort" id="sort-price">Prix</button>
                <button class="btn-sort" id="sort-rating">Notes</button>
                <button class="btn-sort" id="sort-date">Date de publication</button>
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
    <script>
        const subcategories = {
            restauration: ['Française', 'Fruits de mer', 'Asiatique', 'Indienne', 'Italienne', 'Gastronomique', 'Restauration rapide', 'Crêperie'],
            activite: ['Urbain', 'Nature', 'Plein air', 'Culturel', 'Patrimoine', 'Histoire', 'Sport', 'Nautique', 'Gastronomie', 'Musée', 'Atelier', 'Musique', 'Famille'],
            visite: ['Parc d\'attractions'],
            spectacle: ['Cinéma', 'Cirque', 'Son et lumière', 'Humour']
        };

        function showSubcategories() {
            const mainCategory = document.getElementById('main-category').value;
            const subcategoryContainer = document.getElementById('subcategory-list');
            subcategoryContainer.innerHTML = ''; // Reset
            if (mainCategory && subcategories[mainCategory]) {
                subcategories[mainCategory].forEach(subcategory => {
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.id = subcategory;
                    checkbox.name = 'subcategory';
                    checkbox.value = subcategory;

                    const label = document.createElement('label');
                    label.htmlFor = subcategory;
                    label.innerText = subcategory;

                    const wrapper = document.createElement('div');
                    wrapper.appendChild(checkbox);
                    wrapper.appendChild(label);

                    subcategoryContainer.appendChild(wrapper);
                });
                document.getElementById('subcategories').classList.remove('hidden');
            } else {
                document.getElementById('subcategories').classList.add('hidden');
            }
        }

        const sortButtons = document.querySelectorAll('.btn-sort');
        sortButtons.forEach(button => {
            button.addEventListener('click', () => {
                sortButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
            });
        });
    </script>

</body>

</html>
