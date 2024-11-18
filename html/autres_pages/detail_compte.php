<?php
require_once 'component/util.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

[$id] = get_args($_GET, [['id', is_numeric(...)]]);
$membre  = query_membre($id);

if ($membre === false) {
    html_error("l'membre d'ID $id n'existe pas");
}
// Afficher le détail du compte du membre

$pseudo = $membre["pseudo"]; 
$email = $membre["email"];
$nom = $membre["nom"];
$prenom = $membre["prenom"];
$telephone = $membre["telephone"];

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>detail_compte_membre&nbsp;: <?= $id ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <script async src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="/style/style.css">
</head>

<body>
    <?php 
    
    echo "<pre>";
    print_r($membre);
    echo "</pre>";
    

    require 'component/header.php' ?>

    <main>
        
      
    </main>

    <?php require 'component/footer.php' ?>

    
</body>

</html>