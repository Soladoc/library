<?php
require_once 'db.php';
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'ID est présent dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id']; // Cast pour plus de sécurité

    // Connexion à la base de données
    $pdo = db_connect();

    // Préparer la requête pour récupérer les détails de l'offre
    $query = "SELECT * FROM pact._offre WHERE id_offre = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);

    // Récupérer les données de l'offre
    $offre = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si l'offre est trouvée, afficher ses détails
    if ($offre) {
        $titre  = $offre['titre'];  // Assurez-vous que le nom des colonnes correspond à la base de données
        $description = $offre['description_detaille'];
        $adresse = $offre['adresse'];
        $site_web = $offre['url_site_web'];
        
        echo '<h3>' . htmlspecialchars($titre) . '</h3>';
        echo '<p class="location">Adresse ID : ' . htmlspecialchars($adresse) . '</p>';
        echo '<p class="description">Description : ' . htmlspecialchars($description) . '</p>';
        echo '<p class="website">Site web : <a href="' . htmlspecialchars($site_web) . '">' . htmlspecialchars($site_web) . '</a></p>';
    } else {
        echo 'Aucune offre trouvée avec cet ID.';
    }
} else {
    echo 'ID d\'offre invalide.';
}

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>offre : <?php echo $id ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script async src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <?php require 'component/header.php' ?>
    <!-- Offer Details -->
    <main>
        <section class="offer-details">
            <div class="offer-main-photo">
                <img src="../images/offre/Radôme1.jpg" alt="Main Photo" class="offer-photo-large">
                <div class="offer-photo-gallery">
                    <img src="../images/offre/Radôme2.jpg" alt="Photo 2" class="offer-photo-small">
                    <img src="../images/offre/Radôme3.jpg" alt="Photo 3" class="offer-photo-small">
                </div>
            </div>

            <div class="offer-info">
                <h2><?php echo $titre ?></h2>
                <p class="description"><?php echo $description ?></p>
                <div class="offer-status">
                    <!-- <p class="price">Prix : 13-39€</p>
                    <p class="status">Statut : <span class="open">Ouvert</span></p>
                    <p class="rating">Note : ★★★★☆ (4.7/5, 256 avis)</p>
                    <p class="hours">Horaires : 9h30 - 18h30</p>
                    <button class="btn-reserve">Réserver</button> -->
                </div>
            </div>
        </section>

        <!-- Location -->
        <section class="offer-location">
            <h3>Emplacement et coordonnées</h3>
            <div id="map" class="map"></div>
            <div class="contact-info">
                <p><strong>Adresse :</strong> <?php echo $adresse ?></p>
                <p><strong>Site web :</strong> <a href="<?php echo $site_web ?>"><?php echo $site_web ?></a></p>
                <!-- <p><strong>Téléphone :</strong> 02 96 46 63 80</p> -->
            </div>
        </section>

        <!-- User Reviews -->
        <!-- <section class="offer-reviews">
            <h3>Avis des utilisateurs</h3>

             Review Form 
            <div class="review-form">
                <textarea placeholder="Votre avis..."></textarea>
                <label for="rating">Note :</label>
                <select id="rating">
                    <option value="5">5 étoiles</option>
                    <option value="4">4 étoiles</option>
                    <option value="3">3 étoiles</option>
                    <option value="2">2 étoiles</option>
                    <option value="1">1 étoile</option>
                </select>
                <button class="btn-publish">Publier</button>
            </div>

             Summary of reviews 
            <div class="review-summary">
                <h4>Résumé des notes</h4>
                <p>Moyenne : 4.7/5 ★</p>
                <div class="rating-distribution">
                    <p>5 étoiles : 70%</p>
                    <p>4 étoiles : 20%</p>
                    <p>3 étoiles : 7%</p>
                    <p>2 étoiles : 2%</p>
                    <p>1 étoile : 1%</p>
                </div>
            </div>

            List of reviews 
            <div class="review-list">
                <div class="review">
                    <p><strong>Jean Dupont</strong> - ★★★★★</p>
                    <p>Super expérience, je recommande fortement !</p>
                </div>
                <div class="review">
                    <p><strong>Marie Leclerc</strong> - ★★★★☆</p>
                    <p>Une belle visite, mais quelques parties étaient fermées...</p>
                </div>
            </div>
        </section> -->
    </main>
    <?php require 'component/footer.php' ?>

    <script>
    // // OpenStreetMap Integration
    // var map = L.map('map').setView([48.779, -3.518], 13);
    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //     attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    // }).addTo(map);
    // L.marker([48.779, -3.518]).addTo(map)
    //     .bindPopup('Découverte interactive de la cité des Télécoms')
    //     .openPopup();
    // L.marker([45.779, -3.518]).addTo(map)
    //     .bindPopup('hihihihihihihihihui')
    // L.marker([45.779, -4.518]).addTo(map)
    //     .bindPopup('hihihihihihihihihui')
    </script>
</body>

</html>