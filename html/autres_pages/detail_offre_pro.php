<?php
require_once 'db.php';
require_once 'util.php';
require_once 'queries.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'component/ImageView.php';

$args = [
    'id' => getarg($_GET, 'id', arg_filter(FILTER_VALIDATE_INT))
];

$page = new Page("offre : {$args['id']}",
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'],
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' => 'async']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    DB\offre_alterner_etat($args['id']);
    $offre = DB\query_offre($args['id']);
    redirect_to($_SERVER['REQUEST_URI']);
    exit;
}
// Récupérer les données de l'offre
$offre = DB\query_offre($args['id']);

// Si l'offre est trouvée, afficher ses détails
if ($offre) {
    $titre = $offre['titre'];  // Assurez-vous que le nom des colonnes correspond à la base de données
    $description = $offre['description_detaillee'];
    $adresse = $offre['id_adresse'];
    $site_web = $offre['url_site_web'];
    $image_pricipale = notfalse(Image::from_db($offre['id_image_principale']));
    $en_ligne = $offre['en_ligne'];
    $info_adresse = DB\query_adresse($adresse);
    $avis = DB\query_avis();
    $gallerie = DB\query_gallerie($args['id']);
    // Vérifier si l'adresse existe
    if ($info_adresse) {
        // Construire une chaîne lisible pour l'adresse
        $numero_voie = $info_adresse['numero_voie'];
        $complement_numero = $info_adresse['complement_numero'];
        $nom_voie = $info_adresse['nom_voie'];
        $localite = $info_adresse['localite'];
        $code_postal = DB\query_codes_postaux($info_adresse['code_commune'], $info_adresse['numero_departement'])[0];

        // Concaténer les informations pour former une adresse complète
        $adresse_complete = "$numero_voie $complement_numero $nom_voie, $localite, $code_postal";

        // Afficher ou retourner l'adresse complète
    } else {
        echo 'Adresse introuvable.';
    }
} else {
    echo 'Aucune offre trouvée avec cet ID.';
}

?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <!-- Offer Details -->
    <main>
        <section class="modif">
            <form id="toggleForm" method="POST">
                <div class='online'>
                    <div>
                        <?php if ($en_ligne) { ?>
                        <p>Offre en ligne</p>
                        <button type="button" class="hors_ligne" onclick="enableValidate()">Mettre hors ligne</button>
                        <?php } else { ?>
                        <p>Offre hors ligne</p>
                        <button type="button" class="en_ligne" onclick="enableValidate()">Mettre en ligne</button>
                        <?php } ?>
                    </div>
                    <button type="submit" name="valider" class="valider" id="validateButton" disabled>Valider</button>
                </div>
            </form>
            <div class="page_modif">
                <a class="modifier" href="modif_offre.php?id=<?= $args['id']?>&type_offre=<?= $offre['categorie']?>">Modifier</a>
            </div>
            <section class="offer-details">
                <section class="offer-main-photo">
                    <div class="carousel-container">
                        <div class="carousel">
                            <div class="carousel-slide">
                                <?php (new ImageView($image_pricipale))->put_img() ?>
                            </div>
                            <div class="carousel-slide">
                                <?php (new ImageView($image_pricipale))->put_img() ?>
                            </div>

                            <!-- Galerie d'images -->
                            <?php foreach ($gallerie as $id_image): ?>
                                <div class="carousel-slide">
                                    <?php (new ImageView(Image::from_db($id_image)))->put_img() ?>
                                </div>
                            <?php endforeach ?>
                        </div>

                        <!-- Boutons de navigation -->
                        <button class="carousel-prev" aria-label="Image précédente">❮</button>
                        <button class="carousel-next" aria-label="Image suivante">❯</button>
                    </div>
                </section>

                <div class="offer-info">
                <h2><?= htmlspecialchars($titre) ?></h2>
                <p class="description"><?= nl2br(htmlspecialchars($description)) ?></p>
            </div>

        </section>

        <!-- Location -->
        <section class="offer-location">
            <h3>Emplacement et coordonnées</h3>
            <!-- <div id="map" class="map"></div> -->
            <div class="contact-info">
                <p><strong>Adresse&nbsp;:</strong> <?= $adresse_complete ?></p>
                <p><strong>Site web&nbsp;:</strong> <a href="<?= $site_web ?>"><?= $site_web ?></a></p>
                <!-- <p><strong>Téléphone&nbsp;:</strong> 02 96 46 63 80</p> -->
            </div>
        </section>

        <div class="review-list">
            <h4>Avis de la communauté</h4>
            <div class="review-summary">
                <h4>Résumé des notes</h4>
                <p>Nombre d'avis : <?= DB\query_avis_count($args['id']) ?></p>
                <p>Moyenne&nbsp;: <?php if ($offre['note_moyenne'] != null) { echo $offre['note_moyenne']; } else { echo 0; } ?>/5 ★</p>
                <div class="rating-distribution">
                    <?php $avis = DB\query_avis(id_offre: $offre['id']) ?>
                    <p>5 étoiles&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 5)) ?> avis.</p>
                    <p>4 étoiles&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 4)) ?> avis.</p>
                    <p>3 étoiles&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 3)) ?> avis.</p>
                    <p>2 étoiles&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 2)) ?> avis.</p>
                    <p>1 étoile&nbsp;: <?= count(array_filter($avis, fn($a) => $a['note'] === 1)) ?> avis.</p>
                </div>
                <?php if (!empty($avis)) {
                    foreach ($avis as $avis_temp) { ?>
                <div class="review">
                    <p><strong><?= htmlspecialchars($avis_temp['pseudo_auteur']) ?></strong> - <?= htmlspecialchars($avis_temp['note']) ?>/5</p>
                    <p class="review-contexte">Contexte&nbsp;: <?= htmlspecialchars($avis_temp['contexte']) ?></p>
                    <p><?= htmlspecialchars($avis_temp['commentaire']) ?></p>
                    <p class="review-date"><?= htmlspecialchars($avis_temp['date_experience']) ?></p>
                    <?php if (($id_membre_co = Auth\id_membre_connecte()) !== null && $avis_temp['id_membre_auteur'] = $id_membre_co) { ?>
                    <form method="post" action="modifier.php?id=<?= $args['id'] ?>&avis_id=<?= $avis_id ?>">
                        <button type="submit" class="btn-modif">Modifier</button>
                    </form>
                    <?php } ?>
                </div>
                <?php }
                } else { ?>
                <p>Aucun avis pour le moment.&nbsp;</p>
                <?php } ?>
            </div>
            </section>
    </main>
    <?php $page->put_footer() ?>

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
    <script>
    function enableValidate() {
        document.getElementById('validateButton').disabled = false;
    }
    document.getElementById('validateButton').addEventListener('click', function(e) {
        e.preventDefault();
        if (!this.disabled) {
            document.getElementById('toggleForm').submit();
        }
    });
    </script>
</body>

</html>