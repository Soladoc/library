<?php
session_start();
require_once 'db.php';
$pdo = db_connect();

// Compter le nombre total d'offres pour ce professionnel
$stmt = $pdo->prepare('SELECT COUNT(*) FROM pact.offres WHERE id_professionnel = :id_professionnel');
$stmt->execute(['id_professionnel' => $_SESSION['id']]);
$nb_offre = $stmt->fetchColumn();

// Appeler la procédure stockée pour récupérer le nombre d'offres en ligne
$stmt = $pdo->prepare('CALL nb_offres_en_ligne(:id_professionnel)');
$stmt->execute(['id_professionnel' => $_SESSION['id']]);
$offre_en_ligne = $stmt->fetchColumn();

// Calculer les offres hors ligne
$offre_hors_ligne = $nb_offre - $offre_en_ligne;
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
    <?php require 'component/header.php'; ?>
    <main>
        <h1>Accueil Professionnel</h1>
        <h3><?php echo $nb_offre ?> offres</h3>
        <section class="online-offers">
            <h2>Mes offres en ligne</h2>
            <p>Vos offres actuellement disponibles en ligne : <?php echo $offre_en_ligne; ?></p>

            <div class="offer-list">
                <!-- Liste des offres en ligne -->
                <?php
                $stmt = $pdo->prepare('CALL get_offres_en_ligne(:id_professionnel)');
                $stmt->execute(['id_professionnel' => $_SESSION['id']]);
                while ($offre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="offer-card">
                        <img src="<?php echo $offre['image_url']; ?>" alt="Image de l'offre">
                        <h3><?php echo $offre['titre']; ?></h3>
                        <p class="location"><?php echo $offre['localisation']; ?></p>
                        <p class="category"><?php
                            $stmtCat = $pdo->prepare('CALL get_category(:id_offre)');
                            $stmtCat->execute(['id_offre' => $offre['id']]);
                            echo $stmtCat->fetchColumn();
                        ?></p>
                        <p class="rating">Note : <?php
                            $stmtRating = $pdo->prepare('CALL get_moyenne(:id_offre)');
                            $stmtRating->execute(['id_offre' => $offre['id']]);
                            echo $stmtRating->fetchColumn();
                        ?>/5 ★ (<?php
                            $stmtCount = $pdo->prepare('SELECT COUNT(*) FROM avis WHERE offre = :id_offre');
                            $stmtCount->execute(['id_offre' => $offre['id']]);
                            echo $stmtCount->fetchColumn();
                        ?> avis)</p>
                        <a href="<?php echo $offre['lien']; ?>">
                            <button class="btn-more-info">En savoir plus</button>
                        </a>
                    </div>
                    <?php
                }
                $stmt->closeCursor();
                ?>
            </div>
        </section>

        <section class="offline-offers">
            <h2>Mes offres hors ligne</h2>
            <p>Vos offres hors-ligne : <?php echo $offre_hors_ligne; ?> </p>

            <div class="offer-carousel">
                <!-- Liste des offres hors ligne -->
                <?php
                $stmt = $pdo->prepare('CALL get_offres_hors_ligne(:id_professionnel)');
                $stmt->execute(['id_professionnel' => $_SESSION['id']]);
                while ($offre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="offer-card">
                        <img src="<?php echo $offre['image_url']; ?>" alt="Image de l'offre">
                        <h3><?php echo $offre['titre']; ?></h3>
                        <p class="location"><?php echo $offre['localisation']; ?></p>
                        <p class="category"><?php
                            $stmtCat = $pdo->prepare('CALL get_category(:id_offre)');
                            $stmtCat->execute(['id_offre' => $offre['id']]);
                            echo $stmtCat->fetchColumn();
                        ?></p>
                        <p class="rating">Note : <?php
                            $stmtRating = $pdo->prepare('CALL get_moyenne(:id_offre)');
                            $stmtRating->execute(['id_offre' => $offre['id']]);
                            echo $stmtRating->fetchColumn();
                        ?>/5 ★ (<?php
                            $stmtCount = $pdo->prepare('SELECT COUNT(*) FROM avis WHERE offre = :id_offre');
                            $stmtCount->execute(['id_offre' => $offre['id']]);
                            echo $stmtCount->fetchColumn();
                        ?> avis)</p>
                        <a href="<?php echo $offre['lien']; ?>">
                            <button class="btn-more-info">En savoir plus</button>
                        </a>
                    </div>
                    <?php
                }
                $stmt->closeCursor();
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
    <?php require 'component/footer.php'; ?>
</body>
</html>
