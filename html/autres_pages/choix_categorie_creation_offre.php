<?php
require_once 'component/Page.php';

$page = new Page("Choix de l'offre", ['choix_categorie_creation_offre.css']);
?>

<!DOCTYPE html>
<html lang="en">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <form action="creation_offre.php" method="get">
        <!-- Todo: utiliser constant categories -->
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