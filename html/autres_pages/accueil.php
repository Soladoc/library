<?php 
require_once 'db.php' ?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <!-- Section de recherche -->
        <section class="search-section">
            <h1>Accueil</h1>
            <br>
            <div class="search-bar">
                <input type="text" placeholder="Rechercher des activités, restaurants, spectacles...">
                <a href="">
                    <button class="btn-search">Rechercher</button>
                </a>
            </div>
        </section>
    
        <!-- Section des offres à la une -->
        <section class="highlight-offers">
            <h2>Offres à la une</h2>
            <div class="offer-list">
                <?php 
                    // 1. Connexion à la base de données
                    $pdo = db_connect();
                    
                    // 2. Préparer et exécuter la requête SQL pour récupérer toutes les offres
                    $stmtOffres = $pdo->query('SELECT * FROM pact._offre');
                    
                    // 3. Boucler sur les résultats pour afficher chaque offre
                    while ($offre = $stmtOffres->fetch(PDO::FETCH_ASSOC)) {
                        
                        // // Calculer si l'offre ferme bientôt (exemple si elle ferme dans moins d'une heure)
                        // $current_time = new DateTime();  // Heure actuelle
                        // $closing_time = new DateTime($offre['closing_time']);
                        // $closing_soon = ($closing_time > $current_time && $closing_time->diff($current_time)->h < 1);
                        $id_offre = $offre['id'];
                        $titre = $offre['titre'];
                        $id_adresse=  $offre['id_adresse'];
                        $id_professionnel =  $offre['id_professionnel'];
                        $resume = $offre['resume'];

                        $stmt = $pdo->prepare('SELECT * from pact._image where id = ?');
                        $stmt->execute([$offre['id_image_principale']]);
                        $image_pricipale = $stmt->fetch();
                        // $categorie = $offre['category'];
                        // // Calculer si l'offre ferme bientôt (exemple si elle ferme dans moins d'une heure)
                        // $current_time = new DateTime();  // Heure actuelle
                        // $closing_time = new DateTime($offre['closing_time']);
                        // $closing_soon = ($closing_time > $current_time && $closing_time->diff($current_time)->h < 1);

                        // 5. Afficher un message si l'offre ferme bientôt
                        // if ($closing_soon) {
                        //     echo '    <span class="closing-soon">Ferme bientôt à ' . $closing_time->format('H:i') . '</span>';
                        // }

                        // Lien vers plus d'infos sur l'offre (mettre l'URL correcte dans href)
                        $requete = "SELECT * FROM pact._adresse WHERE id = ?";
                        $stmt = $pdo->prepare($requete);
                        $stmt->execute([$id_adresse]);
                        $info_adresse = $stmt->fetch();
                


                        // Vérifier si l'adresse existe
                        if ($info_adresse) {
                            // Construire une chaîne lisible pour l'adresse
                            $numero_voie = $info_adresse['numero_voie'];
                            $complement_numero = $info_adresse['complement_numero'];
                            $nom_voie = $info_adresse['nom_voie'];
                            $localite = $info_adresse['localite'];
                            
                            $stmt = $pdo->prepare('select code_postal from _code_postal where code_insee_commune = ?');
                            $stmt->execute([$info_adresse['code_insee_commune']]);
                            $codes_postaux = array_map(fn($row) => $row['code_postal'], $stmt->fetchAll());
                
                            // Concaténer les informations pour former une adresse complète
                            $adresse_complete = $numero_voie . ' ' . $complement_numero . ' ' . $nom_voie . ', ' . $localite . implode(', ', $codes_postaux);
                
                            // Afficher ou retourner l'adresse complète
                        } else {
                            echo 'Adresse introuvable.';
                        }
                        ?>


                <div class="offer-card">
                <img src="/images_utilisateur/<?= $image_pricipale['id'] ?>" alt="Main Photo" class="offer-photo-large">
                    <h3><?= $titre ?>  </h3>
                    <p class="location"><?= $adresse_complete ?></p>
                    <p><?= $resume ?></p>
                    <!-- <p class="category"><?php //echo $categorie ?></p> -->
                    <a href="/autres_pages/detail_offre.php?id=<?= $id_offre ?>">
                        <button class="btn-more-info">En savoir plus</button>
                    </a>
                </div>
                <?php }?>
            </div>
        </section>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>