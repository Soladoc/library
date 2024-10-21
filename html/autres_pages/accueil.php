<?php ?>




<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <?php
        include("header.php");
    ?>

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
                $sql = "SELECT image, title, location, category, price, rating, reviews, professional, closing_time FROM offres";
                $stmt = $pdo->query($sql); // Exécute la requête SQL

                // 3. Boucler sur les résultats pour afficher chaque offre
                while ($offre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Calculer si l'offre ferme bientôt (exemple si elle ferme dans moins d'une heure)
                    $current_time = new DateTime(); // Heure actuelle
                    $closing_time = new DateTime($offre['closing_time']);
                    $closing_soon = ($closing_time > $current_time && $closing_time->diff($current_time)->h < 1);

                    // 4. Afficher les détails de chaque offre dans du HTML
                    echo '<div class="offer-card">';
                    echo '    <img src="' . htmlspecialchars($offre['image']) . '" alt="' . htmlspecialchars($offre['title']) . '">';
                    echo '    <h3>' . htmlspecialchars($offre['title']) . '</h3>';
                    echo '    <p class="location">' . htmlspecialchars($offre['location']) . '</p>';
                    echo '    <p class="category">' . htmlspecialchars($offre['category']) . '</p>';
                    echo '    <p class="price">Prix : ' . htmlspecialchars($offre['price']) . '</p>';
                    echo '    <p class="rating">Note : ' . htmlspecialchars($offre['rating']) . ' ★ (' . htmlspecialchars($offre['reviews']) . ' avis)</p>';
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
                <!-- Offre 2 -->
                <div class="offer-card">
                    <img src="museum.jpg" alt="Musée de l'Astronomie">
                    <h3>Musée de l'Astronomie</h3>
                    <p class="location">Lannion</p>
                    <p class="category">Musée</p>
                    <p class="price">Prix : 10-25€</p>
                    <p class="rating">Note : 4.8 ★ (80 avis)</p>
                    <p class="professional">Proposé par : Lannion Tourisme</p>
                    <span class="closing-soon">Ferme bientôt à 19h00</span>
                    <a href="">
                        <button class="btn-more-info">En savoir plus</button>
                    </a>
                </div>
                <!-- Offre 3 -->
                <div class="offer-card">
                    <img src="park.jpg" alt="Parc Aventure">
                    <h3>Parc Aventure</h3>
                    <p class="location">Tréguier</p>
                    <p class="category">Loisirs</p>
                    <p class="price">Prix : 15-45€</p>
                    <p class="rating">Note : 4.3 ★ (210 avis)</p>
                    <p class="professional">Proposé par : Parc de l'Aventure</p>
                    <span class="closing-soon">Ferme bientôt à 19h30</span>
                    <a href="">
                        <button class="btn-more-info">En savoir plus</button>
                    </a>
                </div>
           
                <!-- Offre 4 -->
                <div class="offer-card">
                    <img src="aquarium.jpg" alt="Aquarium de Bretagne">
                    <h3>Aquarium de Bretagne</h3>
                    <p class="location">Brest</p>
                    <p class="category">Aquarium</p>
                    <p class="price">Prix : 12-30€</p>
                    <p class="rating">Note : 4.6 ★ (90 avis)</p>
                    <p class="professional">Proposé par : Brest Maritime</p>
                    <a href="">
                        <button class="btn-more-info">En savoir plus</button>
                    </a>
                </div>
                <!-- Offre 5 -->
                <div class="offer-card">
                    <img src="hotel.jpg" alt="Hôtel des Dunes">
                    <h3>Hôtel des Dunes</h3>
                    <p class="location">Quiberon</p>
                    <p class="category">Hébergement</p>
                    <p class="price">Prix : 70-150€</p>
                    <p class="rating">Note : 4.2 ★ (140 avis)</p>
                    <p class="professional">Proposé par : Dunes Resort</p>
                    <button class="btn-more-info">En savoir plus</button>
                </div>
            
                <!-- Offre 6 -->
                <div class="offer-card">
                    <img src="camping.jpg" alt="Camping des Pins">
                    <h3>Camping des Pins</h3>
                    <p class="location">Saint-Malo</p>
                    <p class="category">Camping</p>
                    <p class="price">Prix : 20-60€</p>
                    <p class="rating">Note : 4.7 ★ (65 avis)</p>
                    <p class="professional">Proposé par : Camping Nature</p>
                    <button class="btn-more-info">En savoir plus</button>
                </div>
                <!-- Offre 7 -->
                <div class="offer-card">
                    <img src="restaurant.jpg" alt="Restaurant Le Gourmet">
                    <h3>Restaurant Le Gourmet</h3>
                    <p class="location">Dinan</p>
                    <p class="category">Restauration</p>
                    <p class="price">Prix : 25-55€</p>
                    <p class="rating">Note : 4.9 ★ (230 avis)</p>
                    <p class="professional">Proposé par : Le Gourmet Dinan</p>
                    <span class="closing-soon">Ferme bientôt à 22h00</span>
                    <button class="btn-more-info">En savoir plus</button>
                </div>
            </div>
        </section>
    </main>

    <?php
        include("footer.php");
    ?>
</body>

</html>
