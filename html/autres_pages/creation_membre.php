<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'component/inputs.php';

$page = new Page('Création de compte membre');

function fail(string $error): never
{
    redirect_to('?error=' . urlencode($error));
}

if (isset($_POST['motdepasse'])) {
    $pseudo = $_POST['pseudo'];
    if (DB\query_membre($pseudo)) {
        fail('Ce pseudo est déjà utilisé.');
    }

    $email = $_POST['email'];
    if (DB\query_membre($email) or DB\query_professionnel($email)) {
        fail('Cette adresse e-mail est déjà utilisée.');
    }

    if (strlen($_POST['motdepasse']) > 72) {
        fail('Mot de passe trop long');
    }
    $nomCommune = $_POST['adresse'];

    $stmt = DB\connect()->prepare('SELECT code, numero_departement FROM pact._commune WHERE nom = ?');
    $stmt->execute([$nomCommune]);
    $commune = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$commune) {
        fail("La commune '$nomCommune' n'existe pas.");
    }

    $codeCommune = $commune['code'];
    $numeroDepartement = $commune['numero_departement'];

    $stmt = DB\connect()->prepare(' INSERT INTO pact._adresse (code_commune, numero_departement) VALUES ( ?, ?) RETURNING id');
    $stmt->execute([$codeCommune, $numeroDepartement]);

    $idAdresse = $stmt->fetchColumn();

    $mdp_hash = notfalse(password_hash($_POST['motdepasse'], PASSWORD_DEFAULT));

    $stmt = DB\connect()->prepare('insert into pact.membre (pseudo, nom, prenom, telephone, email, mdp_hash, id_adresse) values (?, ?, ?, ?, ?, ?, ?)');

    $stmt->execute([
        $pseudo,
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $email,
        $mdp_hash,
        $idAdresse
    ]);
    redirect_to(location_connexion());  // todo: passer en GET le pseudo pour l'afficher dans le formulaire connexion, pour que l'utilisateur n'ait pas à le retaper.
} else {
?>
<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
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
                        <input type="text" id="adresse" name="adresse" autocomplete="Lannion" required>
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
                        <button type="submit">Valider</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
    <?php $page->put_footer() ?>
</body>

</html>
<?php
}
?>