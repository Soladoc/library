<?php
require_once 'db.php';
require_once 'component/head.php';
$pdo = db_connect()
?>

<!DOCTYPE html>
<html lang="fr">

<?php put_head('Connexion') ?>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <h1>Connexion</h1>
        <section class="connexion">
            <div class="champ-connexion">
                <br>
                <!-- Formulaire de connexion -->
                <form action="../connexion/login.php" method="POST">
                    <div class="champ">
                        <label for="login">Pseudo ou e-mail *</label>
                        <input id="login" name="login" type="text" placeholder="exemple@mail.fr" required>
                    </div>
                    <br>
                    <div class="champ">
                        <label for="mdp">Mot de passe *</label>
                        <input id="mdp" name="mdp" type="password" placeholder="**********" required>
                    </div>
                    <?php if ($error = $_GET['error'] ?? null) { ?>
                    <p class="error"><?= $error ?></p>
                    <?php } ?>
                    <button type="submit" class="btn-connexion">Se connecter</button>
                </form>
                <br><br>
                <label>Pas de compte&nbsp;?</label>
                <a href="creation_membre.php">
                    <button class="btn-creer">Créer un compte personnel</button>
                </a>
                <label>OU</label>
                <a href="creation_comptePro.php">
                    <button class="btn-creer">Créer un compte professionnel</button>
                </a>
                <br>
            </div>
        </section>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>