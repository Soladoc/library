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
            <form action="" class="champ-connexion">
                <br>
                <div class="champ">
                    <label for="email_conexion">E-mail *</label>
                    <input type="email" name="email_conexion" id="email_conexion"  placeholder="exemple@mail.fr">
                </div>
                <br>
                <div class="champ">
                    <label for="mdp_conexion">Mot de passe *</label>
                    <input type="password" name="mdp_conexion" id="mdp_conexion" placeholder="**********">
                </div>
                <br>
                <button type="submit" class="btn-connexion">Se connecter</button>
                <br><br>
                <p>Pas de compte ?</p>
                <button class="btn-creer">Créer un compte personnel</button>
                <p>OU</p>
                <button class="btn-creer">Créer un compte professionnel</button>
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
