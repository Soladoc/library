<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <!-- Section de recherche -->
        <section class="search-section">
            <h1>Accueil</h1>
            <br>
            <div class="search-bar">
                <input type="text" placeholder="Rechercher des activités, restaurants, spectacles...">
                <a href="">
                    <button class="btn-search">Rechercher</button>
                </a>
            </div>
        </section>

        <!-- Section des offres à la une -->
        <section class="highlight-offers">
            <h2>Offres à la une</h2>
            <div class="offer-list">
                <?php
                    // 1. Connexion à la base de données
                    $pdo = db_connect();

                    // 2. Préparer et exécuter la requête SQL pour récupérer toutes les offres
                    $sql = 'SELECT image, title, location, category, price, rating, reviews, professional, closing_time FROM offres';
                    $stmt = $pdo->query($sql);  // Exécute la requête SQL

                    // 3. Boucler sur les résultats pour afficher chaque offre
                    while ($offre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // Calculer si l'offre ferme bientôt (exemple si elle ferme dans moins d'une heure)
                        $current_time = new DateTime();  // Heure actuelle
                        $closing_time = new DateTime($offre['closing_time']);
                        $closing_soon = ($closing_time > $current_time && $closing_time->diff($current_time)->h < 1);
                        echo 'test';
                        // 4. Afficher les détails de chaque offre dans du HTML
                        echo '<div class="offer-card">';
                        echo '    <img src="' . htmlspecialchars($offre['image']) . '" alt="' . htmlspecialchars($offre['title']) . '">';
                        echo '    <h3>' . htmlspecialchars($offre['title']) . '</h3>';
                        echo '    <p class="location">' . htmlspecialchars($offre['location']) . '</p>';
                        echo '    <p class="category">' . htmlspecialchars($offre['category']) . '</p>';
                        echo '    <p class="professional">Proposé par : ' . htmlspecialchars($offre['professional']) . '</p>';

                        // 5. Afficher un message si l'offre ferme bientôt
                        if ($closing_soon) {
                            echo '    <span class="closing-soon">Ferme bientôt à ' . $closing_time->format('H:i') . '</span>';
                        }

                        // Lien vers plus d'infos sur l'offre (mettre l'URL correcte dans href)
                        echo '    <a href="#">Voir l\'offre</a>';
                        echo '</div>';
                    }
                ?>

                <!-- Offre 1 -->
                <div class="offer-card">
                    <img src="creperie.jpg" alt="Crêperie de l'Abbaye">
                    <h3>Crêperie de l'Abbaye de Beauport</h3>
                    <p class="location">Paimpol</p>
                    <p class="category">Restauration</p>
                    <p class="price">Prix : 13-39€</p>
                    <p class="rating">Note : 4.5 ★ (120 avis)</p>
                    <p class="professional">Proposé par : Parc du Radôme</p>
                    <span class="closing-soon">Ferme bientôt à 18h30</span>
                    <a href="">
                        <button class="btn-more-info">En savoir plus</button>
                    </a>
                </div>
            </div>
        </section>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>