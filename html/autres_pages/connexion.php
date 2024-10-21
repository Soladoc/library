<?php
require_once "../../db.php";
$pdo = db_connect();

// Hacher le mot de passe avant l'insertion
$mdp_hash = password_hash('123', PASSWORD_DEFAULT);

$sql = "INSERT INTO offres(pseudo,existe,email, mdp_hash, nom, prenom, telephone, id_identite) 
        VALUES ('Mange',true,'maelanpotier05@gmail.com', :mdp_hash, 'bob', 'charles', '01226262', 5)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':mdp_hash', $mdp_hash);

// Exécuter l'insertion
if ($stmt->execute()) {
    echo "Insertion réussie !";
} else {
    echo "Erreur lors de l'insertion.";
}
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
        <h1>Connexion</h1>
        <section class="connexion">
            <div class="champ-connexion">
                <br>
                <!-- Formulaire de connexion -->
                <form action="../connexion/login.php" method="POST">
                    <div class="champ">
                        <label for="login">E-mail *</label>
                        <input type="text" name="login" placeholder="exemple@mail.fr" id="login" required>
                    </div>
                    <br>
                    <div class="champ">
                        <label for="mdp">Mot de passe *</label>
                        <input type="password" name="mdp" placeholder="**********" id="mdp" required>
                    </div>
                    <br>
                    <button type="submit" class="btn-connexion">Se connecter</button>
                </form>
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
            </div>
        </section>
    </main>

    <br><br><br>
    <?php
        include("footer.php");
    ?>
</body>

</html>
