<?php
require_once 'queries.php';
require_once 'component/head.php';
require_once 'component/inputs.php';
require_once 'util.php';

function fail(string $error)
{
    header('Location: ?error=' . urlencode($error));
    exit;
}

if (isset($_POST['motdepasse'])) {
    $pseudo = $_POST['pseudo'];
    if (query_membre($pseudo)) {
        fail('Ce pseudo est déjà utilisé.');
    }

    $email = $_POST['email'];
    if (query_membre($email) or query_professionnel($email)) {
        fail('Cette adresse e-mail est déjà utilisée.');
    }

    if (strlen($_POST['motdepasse']) > 72) {
        fail('Mot de passe trop long');
    }
    $args = [
        'adresse'=> getarg($_POST,'adresse')
    ];
    $mdp_hash = notfalse(password_hash($_POST['motdepasse'], PASSWORD_DEFAULT));

    $stmt = db_connect()->prepare('insert into pact.membre (pseudo, nom, prenom, telephone, email, mdp_hash, id_adresse) values (?, ?, ?, ?, ?, ?, ?)');

    $stmt->execute([
        $pseudo,
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $email,
        $mdp_hash,
        $args['adresse']
    ]);
    header('Location: /autres_pages/connexion.php');  // todo: passer en GET le pseudo pour l'afficher dans le formulaire connexion, pour que l'utilisateur n'ait pas à le retaper.
} else {
?>
<!DOCTYPE html>
<html lang="en">

<?php put_head('Création de compte membre') ?>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <h1>Créer un compte membre</h1>
        <section class="connexion">
            <div class="champ-connexion">
                <form action="creation_membre.php" method="post" enctype="multipart/form-data">
                    <div class="champ">
                        <label for="pseudo">Pseudo&nbsp;:</label>
                        <input type="text" id="pseudo" name="pseudo" autocomplete="nickname" required>
                    </div>

                    <div class="champ">
                        <label for="nom">Nom&nbsp;:</label>
                        <input type="text" id="nom" name="nom" autocomplete="family-name" required>
                    </div>

                    <div class="champ">
                        <label for="prenom">Prenom&nbsp;:</label>
                        <input type="text" id="prenom" name="prenom" autocomplete="given-name" required>
                    </div>

                    <div class="champ">
                        <label for="telephone">Téléphone&nbsp;:</label>
                        <input type="tel" id="telephone" name="telephone" placeholder="0123456789" pattern="\d{10}" autocomplete="tel" required>
                    </div>

                    <div class="champ">
                        <label for="adresse">Adresse&nbsp;:</label>
                        <?php put_input_address('adresse','adresse') ?>
                    </div>

                    <div class="champ">
                        <label for="email">Email&nbsp;:</label>
                        <input type="email" id="email" name="email" autocomplete="email" required>
                    </div>

                    <div class="champ">
                        <label for="motdepasse">Mot de passe&nbsp;:</label>
                        <input type="password" id="motdepasse" name="motdepasse" required>
                        <br>
                    </div>
                    <!-- Todo: confirmation mdp -->
                    <p class="error"><?= $_GET['error'] ?? '' ?></p>

                    <div class="champ">
                        <input type="submit" value="Valider">
                    </div>
                </form>
            </div>
        </section>
    </main>
    <?php require 'component/footer.php' ?>
</body>

</html>
<?php
}
?>