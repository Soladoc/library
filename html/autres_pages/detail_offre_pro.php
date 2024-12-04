<?php 
require_once 'db.php';
require_once 'util.php';
require_once 'queries.php';
require_once 'redirect.php';
require_once 'model/Offre.php';
require_once 'component/Page.php';
require_once 'component/ImageView.php';
require_once 'component/InputOffre.php';

$offre = notfalse(Offre::from_db(getarg($_GET, 'id', arg_int())));

$page = new Page("offre : {$offre->id}");


if ($_POST) {
    $offre->alterner_etat();
    redirect_to($_SERVER['REQUEST_URI']);
    exit;
}
$id_membre_co = Auth\exiger_connecte_pro();
?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <!-- Offer Details -->
    <main>
        <section class="modif">
            <div class="bandeau-etat <?= $offre->en_ligne ? 'vert' : 'rouge' ?>">
                <p class="etat"><?= $offre->en_ligne ? 'Offre en ligne' : 'Offre hors ligne' ?></p>
                <button type="button" class="bouton" onclick="enableValidate()">
                    <?= $offre->en_ligne ? 'Mettre hors ligne' : 'Mettre en ligne' ?>
                </button>
                <form id="toggleForm" method="post" style="display: inline;">
                    <button type="submit" name="valider" class="bouton" id="validateButton" disabled>Valider</button>
                </form>
                <a class="bouton modifier" href="modif_offre.php?id=<?= $offre->id ?>&categorie=<?= $offre->categorie ?>">Modifier</a>
            </div>
        </section>
            
        <section class="offer-details">
            <section class="offer-main-photo">
                <div class="carousel-container">
                    <div class="carousel">
                        <div class="carousel-slide">
                            <?php (new ImageView($offre->image_principale))->put_img() ?>
                        </div>
                        <div class="carousel-slide">
                            <?php (new ImageView($offre->image_principale))->put_img() ?>
                        </div>

                            <!-- Galerie d'images -->
                        <?php foreach ($offre->galerie as $image): ?>
                            <div class="carousel-slide">
                                <?php (new ImageView($image))->put_img() ?>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <!-- Boutons de navigation -->
                    <button class="carousel-prev" aria-label="Image précédente">❮</button>
                    <button class="carousel-next" aria-label="Image suivante">❯</button>
                </div>
            </section>

            <div class="offer-info">
                <h2><?= htmlspecialchars($offre->titre) ?></h2>
                <p class="description"><?= nl2br(htmlspecialchars($offre->description_detaillee)) ?></p>
            </div>

        </section>

        <!-- Location -->
        <section class="offer-location">
            <h3>Emplacement et coordonnées</h3>
            <!-- <div id="map" class="map"></div> -->
            <div class="contact-info">
                <p><strong>Adresse&nbsp;:</strong> <?= $offre->adresse->format() ?></p>
                <p><strong>Site web&nbsp;:</strong> <a href="<?= $offre->url_site_web ?>"><?= $offre->url_site_web ?></a></p>
            </div>
        </section>

        <div class="review-list">
            <h4>Avis de la communauté</h4>
            <div class="review-summary">
                <h4>Résumé des notes</h4>
                <p>Nombre d'avis : <?= $offre->nb_avis ?></p>
                <p>Moyenne&nbsp;: <?= $offre->note_moyenne ?? 0 ?>/5 ★</p>
                <div class="rating-distribution">
                    <?php $avis = DB\query_avis(id_offre: $offre->id) ?>
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
                            <?php if ($id_membre_co!= null && $avis_temp['id_membre_auteur'] = $id_membre_co) { ?>
                                <form method="post" action="modifier.php?id=<?= $offre->id ?>&avis_id=<?= $avis_temp['id'] ?>">
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
        if (this.disabled) {
            e.preventDefault();
        }
    });
    </script>
</body>
</html>