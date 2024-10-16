<?php ?>




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
        <section class="connexion">
            <div class="champ-connexion">
                <br>
                <div class="champ">
                    <p>E-mail *</p>
                    <input type="text" placeholder="exemple@mail.fr">
                </div>
                <br>
                <div class="champ">
                    <p>Mot de passe *</p>
                    <input type="text" placeholder="**********">
                </div>
                <br>
                <button class="btn-connexion">Se connecter</button>
                <br><br>
                <p>Pas de compte ?</p>
                <button class="btn-creer">Créer un compte personnel</button>
                <p>OU</p>
                <button class="btn-creer">Créer un compte professionnel</button>
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
