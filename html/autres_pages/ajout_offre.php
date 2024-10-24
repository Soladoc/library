<?php
// Connexion à la base de données

// Initialiser un message vide
$message = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et valider les données du formulaire
    $titre = $_POST['titre'];
    $resume = $_POST['resume'];
    $description_detaille = $_POST['description_detaille'];
    $url_site_web = $_POST['url_site_web'];
    $adresse = (int)$_POST['adresse'];
    $photoprincipale = (int)$_POST['photoprincipale'];
    $abonnement = $_POST['abonnement'];
    $id_signalable = (int)$_POST['id_signalable'];
    $id_professionnel = (int)$_POST['id_professionnel'];

    // Connexion à la base de données
    $pdo = db_connect();

    // Insérer l'offre dans la base de données
    $sql = "INSERT INTO pact._offre (
                titre, resume, description_detaille, url_site_web, adresse, photoprincipale, abonnement, id_signalable, id_professionnel
            ) VALUES (
                :titre, :resume, :description_detaille, :url_site_web, :adresse, :photoprincipale, :abonnement, :id_signalable, :id_professionnel
            )";
    
    $stmt = $pdo->prepare($sql);

    try {
        // Exécution de la requête avec les données du formulaire
        $stmt->execute([
            ':titre' => $titre,
            ':resume' => $resume,
            ':description_detaille' => $description_detaille,
            ':url_site_web' => $url_site_web,
            ':adresse' => $adresse,
            ':photoprincipale' => $photoprincipale,
            ':abonnement' => $abonnement,
            ':id_signalable' => $id_signalable,
            ':id_professionnel' => $id_professionnel
        ]);
        $message = "Offre créée avec succès!";
    } catch (Exception $e) {
        $message = "Erreur lors de la création de l'offre : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Offre</title>
</head>
<body>

    <h1>Créer une nouvelle offre</h1>

    <!-- Afficher un message si disponible -->
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="titre">Titre :</label>
        <input type="text" name="titre" id="titre" required><br><br>

        <label for="resume">Résumé :</label>
        <textarea name="resume" id="resume" maxlength="1023" required></textarea><br><br>

        <label for="description_detaille">Description détaillée :</label>
        <textarea name="description_detaille" id="description_detaille" required></textarea><br><br>

        <label for="url_site_web">URL du site web :</label>
        <input type="url" name="url_site_web" id="url_site_web" maxlength="2047"><br><br>

        <label for="adresse">ID Adresse :</label>
        <input type="number" name="adresse" id="adresse" required><br><br>

        <label for="photoprincipale">ID Photo principale :</label>
        <input type="number" name="photoprincipale" id="photoprincipale" required><br><br>

        <label for="abonnement">Abonnement :</label>
        <input type="text" name="abonnement" id="abonnement" maxlength="63" required><br><br>

        <label for="id_signalable">ID Signalable :</label>
        <input type="number" name="id_signalable" id="id_signalable" required><br><br>

        <label for="id_professionnel">ID Professionnel :</label>
        <input type="number" name="id_professionnel" id="id_professionnel" required><br><br>

        <button type="submit">Créer l'offre</button>
    </form>

</body>
</html>
