<?php
require_once 'component/offre.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

[$id] = get_args($_GET, [['id', is_numeric(...)]]);

$offre = query_offre($id);
if ($offre === false) {
    html_error("l'offre d'ID $id n'existe pas");
}
assert($offre['id'] === $id);

$titre = $offre['titre'];
$description = $offre['description_detaillee'];
$site_web = $offre['url_site_web'];
$image_pricipale = query_image($offre['id_image_principale']);
$adresse = notfalse(query_adresse($offre['id_adresse']));

$gallerie = query_gallerie($id);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>offre&nbsp;: <?= $id ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <script async src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="/style/style.css">
</head>

<body>
    <?php 
    echo "<pre>";
    print_r($gallerie);
    echo "</pre>";
    require 'component/header.php' ?>
    <!-- Offer Details -->
    <main>
        <section class="offer-details">
            <div class="offer-main-photo">
                <?php put_image($image_pricipale) ?>
                 <div class="offer-photo-gallery">
                    <?php 
                    if (count($gallerie)>=1) {
                        put_image(query_image($gallerie[0]));
                        
                    }
                    if (count($gallerie)>=2) {
                        put_image(query_image($gallerie[1]));
                    }
                     
                    ?>
                </div> 
            </div>

            <div class="offer-info">
                <h2><?= $titre ?></h2>
                <p class="description"><?= $description ?></p>
                <div class="offer-status">
                    <!-- <p class="price">Prix&nbsp;: 13-39€</p>
                    <p class="status">Statut&nbsp;: <span class="open">Ouvert</span></p>
                    <p class="rating">Note&nbsp;: ★★★★☆ (4.7/5, 256 avis)</p>
                    <p class="hours">Horaires&nbsp;: 9h30 - 18h30</p>
                    <button class="btn-reserve">Réserver</button> -->
                </div>
            </div>
        </section>

        <!-- Location -->
        <section class="offer-location">
            <h3>Emplacement et coordonnées</h3>
            <!-- <div id="map" class="map"></div> -->
            <div class="contact-info">
                <p><strong>Adresse&nbsp;:</strong> <?= format_adresse($adresse) ?></p>
                <p><strong>Site web&nbsp;:</strong> <a href="<?= $site_web ?>"><?= $site_web ?></a></p>
                <!-- <p><strong>Téléphone&nbsp;:</strong> 02 96 46 63 80</p> -->
            </div>
        </section>

        <!-- User Reviews -->
        <!-- <section class="offer-reviews">
            <h3>Avis des utilisateurs</h3>

             Review Form 
            <div class="review-form">
                <textarea placeholder="Votre avis..."></textarea>
                <label for="rating">Note&nbsp;:</label>
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
                <p>Moyenne&nbsp;: 4.7/5 ★</p>
                <div class="rating-distribution">
                    <p>5 étoiles&nbsp;: 70%</p>
                    <p>4 étoiles&nbsp;: 20%</p>
                    <p>3 étoiles&nbsp;: 7%</p>
                    <p>2 étoiles&nbsp;: 2%</p>
                    <p>1 étoile&nbsp;: 1%</p>
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