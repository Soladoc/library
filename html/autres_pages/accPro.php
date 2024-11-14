<?php
session_start();
require_once 'component/offre.php';
require_once 'auth.php';

$ID_PRO = exiger_connecte_pro();

$OFFER_COUNT = query_offres_count($ID_PRO);
$ONLINE_OFFER_COUNT = query_offres_count($ID_PRO, en_ligne: true)
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil Professionnel</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

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
                    $ONLINE_OFFERS = query_offres($ID_PRO, en_ligne: true);
                    while ($offer = $ONLINE_OFFERS->fetch()) {
                        put_card_offre_pro($offer);
                    }
                    notfalse($ONLINE_OFFERS->closeCursor())
                ?>
                <!-- Offre en ligne 2 -->
                <div class="offer-card">
                    <img src="vallee_saints.jpg" alt="Randonnée dans la vallée des Saints">
                    <h3>Randonnée dans la vallée des Saints</h3>
                    <p class="location">Boudes (63340)</p>
                    <p class="category">Restauration</p>
                    <p class="rating">Note&nbsp;: 4.2/5 ★ (54 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>
                </div>

            </div>
        </section>

        <section class="offline-offers">
            <h2>Mes offres hors ligne</h2>
            <p>Vos offres hors-ligne&nbsp;: <?= $OFFER_COUNT - $ONLINE_OFFER_COUNT ?> </p>

            <div class="offer-carousel">
                <?php
                    $OFFRES_HORS_LIGNE = query_offres($ID_PRO, en_ligne: false);
                    while ($offer = $OFFRES_HORS_LIGNE->fetch()) {
                        put_card_offre_pro($offer);
                    }
                    $OFFRES_HORS_LIGNE->closeCursor()
                ?>
                <div class="offer-card">
                    <img src="telecom.jpg" alt="Découverte interactive de la cité des Télécoms">
                    <h3>Découverte interactive de la cité des Télécoms</h3>
                    <p class="location">Pleumeur-Bodou (22560)</p>
                    <p class="category">Restauration</p>
                    <p class="rating">Note&nbsp;: 4.7/5 ★ (256 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>

                </div>

                <!-- Offre 2 -->
                <div class="offer-card">
                    <img src="vallee_saints.jpg" alt="Randonnée dans la vallée des Saints">
                    <h3>Randonnée dans la vallée des Saints</h3>
                    <p class="location">Boudes (63340)</p>
                    <p class="category">Restauration</p>
                    <p class="rating">Note&nbsp;: 4.2/5 ★ (54 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>
                </div>

                <!-- Offre 3 -->
                <div class="offer-card">
                    <img src="grenouilles.jpg" alt="Chasse aux grenouilles dans le Lac du Gourgal">
                    <h3>Chasse aux grenouilles dans le Lac du Gourgal</h3>
                    <p class="location">Guingamp (22200)</p>
                    <p class="category">Activité Nature</p>
                    <p class="rating">Note&nbsp;: 3.7/5 ★ (122 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>
                </div>

                <!-- Offre 4 -->
                <div class="offer-card">
                    <img src="char_voile.jpg" alt="Initiation au Char à Voile sur la plage">
                    <h3>Initiation au Char à Voile sur la plage</h3>
                    <p class="location">Pléneuf-Val-André (22370)</p>
                    <p class="category">Sport nautique</p>
                    <p class="rating">Note&nbsp;: 4.4/5 ★ (24 avis)</p>
                    <button class="btn-more-info" href="">En savoir plus</button>
                </div>
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