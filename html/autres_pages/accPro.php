<?php
require_once 'component/offre.php';
require_once 'component/head.php';
require_once 'auth.php';

$id_professionnel = exiger_connecte_pro();

$nb_offres = query_offres_count($id_professionnel);
$nb_offres_en_ligne = query_offres_count($id_professionnel, en_ligne: true)
?>

<!DOCTYPE html>
<html lang="fr">

<?php put_head('Accueil Professionnel') ?>

<body>
    <?php require 'component/header.php' ?>
    <main>

        <h1>Accueil Professionnel</h1>
        <a class="btn-more-info" href="/autres_pages/choix_categorie_creation_offre.php">Créer une offre</a>

        <h3><?= $nb_offres ?> offres</h3>
        <section class="online-offers">
            <h2>Mes offres en ligne</h2>
            <p>Vos offres actuellement disponibles en ligne&nbsp;: <?= $nb_offres_en_ligne ?></p>

            <div class="offer-list">
                <?php
                $offres_en_ligne = query_offres($id_professionnel, en_ligne: true);
                while ($offre = $offres_en_ligne->fetch()) {
                    put_card_offre_pro($offre);
                }
                notfalse($offres_en_ligne->closeCursor())
                ?>
            </div>
        </section>

        <section class="offline-offers">
            <h2>Mes offres hors ligne</h2>
            <p>Vos offres hors-ligne&nbsp;: <?= $nb_offres - $nb_offres_en_ligne ?> </p>

            <div class="offer-carousel">
                <?php
                $offres_hors_ligne = query_offres($id_professionnel, en_ligne: false);
                while ($offre = $offres_hors_ligne->fetch()) {
                    put_card_offre_pro($offre);
                }
                $offres_hors_ligne->closeCursor()
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