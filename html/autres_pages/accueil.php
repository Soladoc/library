<?php 
require_once 'db.php';
?>


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
                    $sql = 'SELECT * FROM pact._offre';
                    $stmt = $pdo->query($query);  // Exécution de la requête

                    // Récupérer toutes les offres
                    $offres = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Débogage pour vérifier le contenu récupéré
                    if ($offres) {
                        echo 'Nombre d\'offres récupérées : ' . count($offres) . '<br><br>';

                    // 3. Boucler sur les résultats pour afficher chaque offre
                    foreach ($offres as $offre) {
                        // // Calculer si l'offre ferme bientôt (exemple si elle ferme dans moins d'une heure)
                        // $current_time = new DateTime();  // Heure actuelle
                        // $closing_time = new DateTime($offre['closing_time']);
                        // $closing_soon = ($closing_time > $current_time && $closing_time->diff($current_time)->h < 1);
                        $id_offre = $offre['id_offre'];
                        $image = $offre['photoprincipale'];
                        $titre = $offre['titre'];
                        $adresse=  $offre['adresse'];
                        $id_professionnel =  $offre['id_professionnel'];
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
                        $query = "SELECT * FROM pact._adresse WHERE id_adresse = :id";
                        $stmt = $pdo->prepare($query);
                        $stmt->execute(['id' => $adresse]);
                        $info_adresse = $stmt->fetch(PDO::FETCH_ASSOC);
                


                        // Vérifier si l'adresse existe
                        if ($info_adresse) {
                            // Construire une chaîne lisible pour l'adresse
                            $numero_voie = $info_adresse['numero_voie'];
                            $complement_numero = $info_adresse['complement_numero'];
                            $nom_voie = $info_adresse['nom_voie'];
                            $localite = $info_adresse['localite'];
                            $code_postal = $info_adresse['commune_code_postal'];
                
                            // Concaténer les informations pour former une adresse complète
                            $adresse_complete = $numero_voie . ' ' . $complement_numero . ' ' . $nom_voie . ', ' . $localite . ', ' . $code_postal;
                
                            // Afficher ou retourner l'adresse complète
                        } else {
                            echo 'Adresse introuvable.';
                        }
                        ?>


                <div class="offer-card">
                    <!-- <img src="<?php //echo $offre['image']?>" alt="Crêperie de l'Abbaye"> -->
                    <h3><?php echo $titre ?>  </h3>
                    <p class="location"><?php echo $adresse_complete ?></p>
                    <!-- <p class="category"><?php //echo $categorie ?></p> -->
                    <a href="https://413.ventsdouest.dev/autres_pages/detail_offre.php?id=<?php echo $id_offre ?>">
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