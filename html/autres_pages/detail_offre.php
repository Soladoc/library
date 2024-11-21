<?php
require_once 'auth.php';
require_once 'component/offre.php';
require_once 'component/head.php';

$args = [
    'id' => getarg($_GET, 'id', arg_filter(FILTER_VALIDATE_INT)),
];

if ($_POST) {
    $args += [
        'commentaire' => getarg($_POST, 'commentaire'),
        'date_avis' => getarg($_POST, 'date'),
        'note' => getarg($_POST, 'rating', arg_filter(FILTER_VALIDATE_INT)),
        'contexte' => getarg($_POST, 'contexte'),
    ];
    if(($id_membre_co = id_membre_connecte()) === null){
        $error_message = "Veuillez vous connecter pour publier un avis.";
    }
    else {
        $querry="INSERT INTO pact.avis (id_membre_auteur,id_offre,commentaire,date_experience,note,contexte) VALUES (?,?,?,?,?,?);";
        $stmt = db_connect()->prepare($querry);
        $stmt->execute([
            $id_membre_co,
            $args['id'] ,
            $args['commentaire'],
            $args['date_avis'],
            $args['note'],
            $args['contexte']
        ]);
        $success_message = "Avis ajouté avec succès !";
    }
}

$offre = query_offre($args['id']);
if ($offre === false) {
    put_head("Erreur ID",
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'],
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' => 'async']);
    require 'component/header.php';
    echo "</br></br>L'offre que vous cherchez n'existe pas";
    ?>
    <script>console.log("Pas d'offre n°<?php echo $args['id'] ?>")</script>
    <?php
    require 'component/footer.php';
    exit; 
}
assert($offre['id'] === $args['id']);

$titre = $offre['titre'];
$description = $offre['description_detaillee'];
$site_web = $offre['url_site_web'];
$image_pricipale = query_image($offre['id_image_principale']);
$adresse = notfalse(query_adresse($offre['id_adresse']));

$gallerie = query_gallerie($args['id']);
$avis=query_avis()
?>

<!DOCTYPE html>
<html lang="fr">

<?php put_head("offre : {$args['id']}",
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'],
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' => 'async']); 
?>

<body>
    <?php
    //TODO suprimmer ca quand romain aura sort that out
    // echo '<pre>';
    // print_r($gallerie);
    // echo '</pre>';
    
    require 'component/header.php'
    ?>
    <!-- Offer Details -->
    <main>
        <section class="offer-details">
            <div class="offer-main-photo">
                <?php put_image($image_pricipale) ?>
                 <div class="offer-photo-gallery">
                    <?php
                    foreach ($gallerie as $image) {
                        put_image(query_image($image));
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

        <section class="offer-reviews">
            <h3>Avis des utilisateurs</h3>

            <!-- Formulaire d'avis -->
            <div class="review-form">
                <div class="message">
                    <?php if (isset($error_message)): ?>
                    <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
                    <?php elseif (isset($success_message)): ?>
                    <p class="success-message"><?= htmlspecialchars($success_message) ?></p>
                    <?php endif; ?>
                </div>
                <form method="post" action="detail_offre.php?id=<?= $args['id'] ?>">
                    <textarea name="commentaire" placeholder="Votre avis..." required></textarea>
                    <label for="rating">Note&nbsp;:</label>
                    <select name="rating" id="rating" required>
                        <option value="5">5 étoiles</option>
                        <option value="4">4 étoiles</option>
                        <option value="3">3 étoiles</option>
                        <option value="2">2 étoiles</option>
                        <option value="1">1 étoile</option>
                    </select>
                    <label for="contexte">Contexte&nbsp;:</label>
                    <select name="contexte" id="contexte" required>
                        <option value="affaires">Affaires</option>
                        <option value="couple">Couple</option>
                        <option value="solo">Solo</option>
                        <option value="famille">Famille</option>
                        <option value="amis">Amis</option>
                    </select>
                    <label for="date">Date de votre visite</label>
                    <input type="date" id="date" name="date" required>
                    </br>
                    <label for="consent">Je certifie que l'avis reflète mes propres expérience et opinion sur cette offre.</label>
                    <input type="checkbox" name="consent" required>
                    <button type="submit" class="btn-publish">Publier</button>
                </form>
            </div>

            <!-- Liste des avis -->
            <div class="review-list">
                <h4>Avis de la communauté</h4>
                <div class="review-summary">
                <h4>Résumé des notes</h4>
                <p>Nombre d'avis : <?=query_avis_count($args['id']) ?></p>
                <p>Moyenne&nbsp;: <?php if ($offre['note_moyenne']!=null){echo round($offre['note_moyenne'],2);} else {echo 0;} ?>/5 ★</p>
                <div class="rating-distribution">
                    <?php $avis = query_avis(id_offre: $offre['id']); ?>
                    <p>5 étoiles&nbsp;: <?=count(array_filter($avis, fn($a) => $a['note'] === 5)) ?> avis.</p>
                    <p>4 étoiles&nbsp;: <?=count(array_filter($avis, fn($a) => $a['note'] === 4)) ?> avis.</p>
                    <p>3 étoiles&nbsp;: <?=count(array_filter($avis, fn($a) => $a['note'] === 3)) ?> avis.</p>
                    <p>2 étoiles&nbsp;: <?=count(array_filter($avis, fn($a) => $a['note'] === 2)) ?> avis.</p>
                    <p>1 étoile&nbsp;: <?=count(array_filter($avis, fn($a) => $a['note'] === 1)) ?> avis.</p>
                </div>
                <?php if (!empty($avis)) {
                    foreach ($avis as $avis_temp) { ?>
                        <div class="review">
                            <p><strong><?= htmlspecialchars($avis_temp['pseudo_auteur']) ?></strong> - <?= htmlspecialchars($avis_temp['note']) ?>/5</p>
                            <p class="review-contexte">Contexte&nbsp;: <?= htmlspecialchars($avis_temp['contexte']) ?></p>
                            <p><?= htmlspecialchars($avis_temp['commentaire']) ?></p>
                            <p class="review-date"><?= htmlspecialchars($avis_temp['date_experience']) ?></p>
                            <?php if ($avis_temp['id_membre_auteur']==exiger_connecte_membre()) { ?>
                            <form method="post" action="../avis/modifier.php?avis_id=<?= $avis_temp['id'] ?>&offre=<?=$args['id'] ?>">
                                <button type="submit" class="btn-modif">Modifier</button>
                            </form>
                            <?php } ?> 
                        </div>
                    <?php } 
                } else { ?>
                    <p>Aucun avis pour le moment. Soyez le premier à en écrire un&nbsp;!</p>
                <?php } ?>
            </div>
        </section>
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