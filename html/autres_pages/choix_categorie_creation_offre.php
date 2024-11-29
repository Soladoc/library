<?php
require_once 'const.php';
require_once 'component/Page.php';
$page = new Page("Choix de l'offre", ['choix_categorie_creation_offre.css']);
?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <form action="creation_offre.php" method="get">
        <p>Choisissez la catégorie de votre offre</p>
        <?php foreach (array_keys(CATEGORIES_OFFRE) as $categorie) { ?> 
            <button type="submit" name="type_offre" value="<?= $categorie ?>"><?= ucfirst($categorie) ?></button>
        <?php } ?>
    </form>

</body>

</html>
<!--
    @brief: fichier qui redirige un professionnel vers la pagef de création adapté a l'offre qu'il 
    shouaite crééer
    @author: Benjamin dummont-Girard

-->