<?php
require_once "../../db.php";
 $pdo=db_connect();
 $sql = "INSERT INTO _compte(email, mdp_hash, nom, prenom,telephone,id_identite) VALUES ('maelanpotier05@gmail.com',123,'bob','charles','01226262',5)";
 $stmt = $pdo->prepare($sql);    
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>

    <?php
        include("header.php");
    ?>

    <main>
        <!-- Section des offres à la une -->
        <h1>Connexion</h1>
        <section class="connexion" action="../connexion/login.php" method="post" enctype="multipart/form-data">
            <div class="champ-connexion">
                <br>
                <form action="crea.php" method="post" enctype="multipart/form-data">
                <div class="champ">
                    <label for="login">E-mail *</label>
                    <input type="text" placeholder="exemple@mail.fr" id="login" required>
                </div>
                <br>
                <div class="champ">
                    <label for="mdp">Mot de passe *</label>
                    <input type="text" placeholder="**********" id="mdp" required>
                </div>
                <br>
                </form>
                <!-- php -->
                <a href="">
                    <button class="btn-connexion">Se connecter</button>
                </a>
                <br><br>
                <label>Pas de compte ?</label>
                <a href="creation_membre.php">
                    <button class="btn-creer">Créer un compte personnel</button>
                </a>
                <label>OU</label>
                <a href="creation_comptePro.php">
                    <button class="btn-creer">Créer un compte professionnel</button>
                </a>
                <br>
            </form>
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
