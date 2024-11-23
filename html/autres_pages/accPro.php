<?php
require_once 'component/offre.php';
require_once 'component/head.php';
require_once 'auth.php';

$id_professionnel = exiger_connecte_pro();

$OFFER_COUNT = query_offres_count($id_professionnel);
$ONLINE_OFFER_COUNT = query_offres_count($id_professionnel, en_ligne: true)
?>

<!DOCTYPE html>
<html lang="fr">

<?php put_head('Accueil Professionnel') ?>

<body>
    <?php require 'component/header.php' ?>
    <main>

        <h1>Accueil Professionnel</h1>
        <a class="btn-more-info" href="/autres_pages/choix_categorie_creation_offre.php">Créer une offre</a>

        <h3><?= $OFFER_COUNT ?> offres</h3>
        <section class="online-offers">
            <h2>Mes offres en ligne</h2>
            <p>Vos offres actuellement disponibles en ligne&nbsp;: <?= $ONLINE_OFFER_COUNT ?></p>

            <div class="offer-list">
                <?php
                    $ONLINE_OFFERS = query_offres($id_professionnel, en_ligne: true);
                    while ($offre = $ONLINE_OFFERS->fetch()) {
                        put_card_offre_pro($offre);
                    }
                    notfalse($ONLINE_OFFERS->closeCursor())
                ?>
            </div>
        </section>

        <section class="offline-offers">
            <h2>Mes offres hors ligne</h2>
            <p>Vos offres hors-ligne&nbsp;: <?= $OFFER_COUNT - $ONLINE_OFFER_COUNT ?> </p>

            <div class="offer-carousel">
                <?php
                    $OFFRES_HORS_LIGNE = query_offres($id_professionnel, en_ligne: false);
                    while ($offre = $OFFRES_HORS_LIGNE->fetch()) {
                        put_card_offre_pro($offre);
                    }
                    $OFFRES_HORS_LIGNE->closeCursor()
                ?>
            </div>
        </section>

        <!-- Bouton pour créer une nouvelle offre -->
        <a href="choix_categorie_creation_offre.php">
            <div class="create-offer">
                <button class="btn-create">Créer une offre</button>
            </div>
        </a>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>