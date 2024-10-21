<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choix de l'offre</title>
    <link rel="stylesheet" href="/style/style.css">
    <link rel="stylesheet" href="/style/choix_categorie_creation_offre.css">
</head>
<body>
    <?php include ('header.php'); ?>
    <form action="creation_offre.php" method="post">
        <p>Choisissez la catégorie de votre offre</p>
        <button type="submit" name="type_offre" value="spectacle">Spectacle</button>
        <button type="submit" name="type_offre" value="parc_attraction">Parc d'atraction</button>
        <button type="submit" name="type_offre" value="visite">Visite</button>
        <button type="submit" name="type_offre" value="restauration">Restauration</button>
        <button type="submit" name="type_offre" value="activite">Activité</button>
    </form>

</body>
</html>
<!--
    @brief: fichier qui redirige un professionel vers la pagef de création adapté a l'offre qu'il 
    shouaite crééer
    @author: Benjamin dummont-Girard

-->