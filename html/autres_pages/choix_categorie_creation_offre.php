<?php
require_once 'component/head.php';
?>

<!DOCTYPE html>
<html lang="en">

<?php put_head("Choix de l'offre", ['choix_categorie_creation_offre.css']) ?>

<body>
    <?php require 'component/header.php' ?>
    <form action="creation_offre.php" method="get">
        <p>Choisissez la catégorie de votre offre</p>
        <button type="submit" name="type_offre" value="spectacle">Spectacle</button>
        <button type="submit" name="type_offre" value="parc-attractions">Parc d'attraction</button>
        <button type="submit" name="type_offre" value="visite">Visite</button>
        <button type="submit" name="type_offre" value="restaurant">Restaurant</button>
        <button type="submit" name="type_offre" value="activite">Activité</button>
    </form>

</body>

</html>
<!--
    @brief: fichier qui redirige un professionel vers la pagef de création adapté a l'offre qu'il 
    shouaite crééer
    @author: Benjamin dummont-Girard

-->