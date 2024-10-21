<?php

require_once "../../db.php";
if (isset($_POST['table'])) {

    print 'Votre nom :'.$_POST['table'];
    $table = $_POST['table'];


    $pdo=db_connect();
    $stmt = $pdo->prepare(query: 'SELECT COUNT(*) FROM pact.' . $table );
    $stmt->execute();

    $count = $stmt->fetchColumn(); // Récupérer le nombre de lignes
    echo "Nombre de lignes dans la table $table : $count";
}
else {
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte pro</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <?php
        include("header.php");
    ?>

    <main>
        <!-- Section des offres à la une -->
        <h1>Créer un compte professionnel</h1>
        <section class="connexion">
                <div class="champ-connexion">
                <form action="regardeMaGrosseBdd.php" method="post" enctype="multipart/form-data">

                    <br>
                    <div class="champ">
                        <p>donne le nom de la table </p>
                        <input type="text" placeholder="" id="table" name="table" required>
                    </div>
                   
                    <button type="submit" class="btn-connexion" >regarde</button>
                </form>
            <br /><br>
            </a>
            <br>
            </div>
        </section>
    </main>
    <br>
    <br>
    <br>
    <?php
        include("footer.php");
    ?>


</body>


</html>
<?php
}
?>
