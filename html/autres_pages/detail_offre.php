<?php
require_once 'auth.php';
require_once 'component/ImageView.php';
require_once 'component/InputNote.php';
require_once 'component/Page.php';
require_once 'db.php';
require_once 'model/Avis.php';
require_once 'model/AvisRestaurant.php';
require_once 'model/Offre.php';
require_once 'model/Restaurant.php';
require_once 'model/Date.php';
require_once 'model/Membre.php';
require_once 'queries.php';

$offre = notfalse(Offre::from_db(getarg($_GET, 'id', arg_int())));

$page = new Page($offre->titre, scripts: ['carousel.js' => 'defer']);

$input_rating = new InputNote(name: 'rating');
if ($offre instanceof Restaurant) {
    $input_note_cuisine      = new InputNote(name: 'note_cuisine');
    $input_note_service      = new InputNote(name: 'note_service');
    $input_note_ambiance     = new InputNote(name: 'note_ambiance');
    $input_note_qualite_prix = new InputNote(name: 'note_qualite_prix');
}

$id_membre_co = Auth\id_membre_connecte();

if ($_POST) {
    if (null === $id_membre_co) {
        $error_message = 'Veuillez vous connecter pour publier un avis.';
        header("Location: #review-form");
    } else if (Avis::from_db_single($id_membre_co, $offre->id)) {
        $error_message = "Vous pouvez ne publier qu'un avis.";
    } else {
        $args_avis = [
            null,
            getarg($_POST, 'commentaire'),
            $input_rating->get($_POST),
            Date::parse(getarg($_POST, 'date')),
            getarg($_POST, 'contexte'),
            Membre::from_db($id_membre_co),
            $offre,
        ];
        $avis = $offre instanceof Restaurant
            ? new AvisRestaurant(
                $args_avis,
                $input_note_cuisine->get($_POST),
                $input_note_service->get($_POST),
                $input_note_ambiance->get($_POST),
                $input_note_qualite_prix->get($_POST),
            )
            : new Avis(...$args_avis);
        $avis->push_to_db();
        $success_message = 'Avis ajouté avec succès !';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php
    $page->put_header();
    ?>
    <!-- Offer Details -->
    <main>
        <section class="offer-details">
            <section class="offer-main-photo">
                <div class="carousel-container">
                    <div class="carousel">
                        <!-- Image principale -->
                        <div class="carousel-slide">
                            <?php (new ImageView($offre->image_principale))->put_img() ?>
                        </div>

                        <!-- Galerie d'images -->
                        <?php foreach ($offre->galerie->images as $image): ?>
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

        <section class="offer-reviews">

            <section class="offer-location">
                <h3>Emplacement et coordonnées</h3>
                <!-- <div id="map" class="map"></div> -->
                <div class="contact-info">
                    <p><strong>Adresse&nbsp;:</strong> <?= $offre->adresse->format() ?></p>
                    <p><strong>Site web&nbsp;:</strong> <a href="<?= $offre->url_site_web ?>"><?= $offre->url_site_web ?></a></p>
                    <p><strong>Téléphone&nbsp;:</strong> 02 96 46 63 80</p>
                </div>
            </section>

            <!-- Formulaire d'avis -->
            <div class="review-form" id="review-form">
                <h3>Laisser un avis</h3><br>
                <div class="message">
                    <?php if (isset($error_message)): ?>
                        <p class="error"><?= htmlspecialchars($error_message) ?></p>
                    <?php elseif (isset($success_message)): ?>
                        <p class="success"><?= htmlspecialchars($success_message) ?></p>
                    <?php endif ?>
                </div>
                <form method="post">
                    <textarea name="commentaire" placeholder="Votre avis..." required></textarea>
                    <label>Note&nbsp;: <?php $input_rating->put() ?></label>
                    <?php if ($offre instanceof Restaurant) { ?>
                        <label>Note cuisine&nbsp;: <?php $input_note_cuisine->put() ?></label>
                        <label>Note service&nbsp;: <?php $input_note_service->put() ?></label>
                        <label>Note ambiance&nbsp;: <?php $input_note_ambiance->put() ?></label>
                        <label>Note qualité prix&nbsp;: <?php $input_note_qualite_prix->put() ?></label>
                    <?php } ?>
                    <label for="contexte">Contexte&nbsp;:</label>
                    <select name="contexte" id="contexte" required>
                        <option value="affaires">Affaires</option>
                        <option value="couple">Couple</option>
                        <option value="solo">Solo</option>
                        <option value="famille">Famille</option>
                        <option value="amis">Amis</option>
                    </select>
                    <label for="date">Date de votre visite</label>
                    <input type="date" id="date" name="date" min="<?= $offre->creee_le->format_date() ?>" value="<?= date('Y-m-d') ?>" required>
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
                    <p>Nombre d'avis : <?= $offre->nb_avis ?></p>
                    <p>Moyenne&nbsp;: <?php if ($offre->note_moyenne !== null) {
    echo round($offre->note_moyenne, 2);
} else {
    echo 0;
} ?>/5 ★</p>
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
                                <?php if ($id_membre_co !== null && $avis_temp['id_membre_auteur'] === $id_membre_co) { ?>
                                    <form method="post" action="/avis/modifier.php?avis_id=<?= $avis_temp['id'] ?>&offre=<?= $offre->id ?>">
                                        <button type="submit" class="btn-modif">Modifier</button>
                                        <button type="submit" name="action" value="supprimer">Supprimer</button>
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
</body>

</html>