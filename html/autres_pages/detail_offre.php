<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'offre - Parc du Radôme</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <!-- Header -->
    <?php include("header.php");?>

    <!-- Offer Details -->
    <main>
        <section class="offer-details">
            <div class="offer-main-photo">
                <img src="offer1-photo1.jpg" alt="Main Photo" class="offer-photo-large">
                <div class="offer-photo-gallery">
                    <img src="offer1-photo2.jpg" alt="Photo 2" class="offer-photo-small">
                    <img src="offer1-photo3.jpg" alt="Photo 3" class="offer-photo-small">
                </div>
            </div>

            <div class="offer-info">
                <h2>Découverte interactive de la cité des Télécoms</h2>
                <p class="description">Venez découvrir l'histoire passionnante des télécommunications dans un cadre unique et interactif à Pleumeur-Bodou.</p>
                <div class="offer-status">
                    <p class="price">Prix : 13-39€</p>
                    <p class="status">Statut : <span class="open">Ouvert</span></p>
                    <p class="rating">Note : ★★★★☆ (4.7/5, 256 avis)</p>
                    <p class="hours">Horaires : 9h30 - 18h30</p>
                    <button class="btn-reserve">Réserver</button>
                </div>
            </div>
        </section>

        <!-- Location -->
        <section class="offer-location">
            <h3>Emplacement et coordonnées</h3>
            <div id="map" class="map"></div>
            <div class="contact-info">
                <p><strong>Adresse :</strong> Pleumeur-Bodou (22560), Bretagne, France</p>
                <p><strong>Site web :</strong> <a href="https://parcduradome.com">https://parcduradome.com</a></p>
                <p><strong>Téléphone :</strong> 02 96 46 63 80</p>
            </div>
        </section>

        <!-- User Reviews -->
        <section class="offer-reviews">
            <h3>Avis des utilisateurs</h3>

            <!-- Review Form -->
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

            <!-- Summary of reviews -->
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

            <!-- List of reviews -->
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
        </section>
    </main>
    <!-- Footer -->
     <?php include("footer.php");?>

    <script>
        // OpenStreetMap Integration
        var map = L.map('map').setView([48.779, -3.518], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([48.779, -3.518]).addTo(map)
            .bindPopup('Découverte interactive de la cité des Télécoms')
            .openPopup();
        L.marker([45.779, -3.518]).addTo(map)
            .bindPopup('hihihihihihihihihui')
            L.marker([45.779, -4.518]).addTo(map)
            .bindPopup('hihihihihihihihihui')
    
    </script>
</body>

</html>
