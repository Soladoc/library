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
                    <label for="login"> E-mail de récuperation:*</label>
                    <input id="login" name="login" type="text" placeholder="exemple@mail.fr" required>
                </div>
                <?php if ($error = $_GET['error'] ?? null) { ?>
                <p class="error"><?= $error ?></p>
                <?php } ?>
                <button type="submit" class="btn-connexion">Se connecter</button>
            </form>
            <br><br>
            <a href="connexion.php">
                <button class="btn-creer">Retour</button>
            </a>
            <br>
        </div>
    </section>
</main>
<?php require 'component/footer.php' ?>
</body>

</html>