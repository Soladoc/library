<?php
require_once 'db.php';
require_once 'util.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'component/inputs.php';

$page = new Page('CoCréer un compte pronnexion');

function fail(string $error)
{
    redirect_to('?error=' . urlencode($error));
    exit;
}

if ($_POST) {
    $args = [
        'nom' => getarg($_POST, 'nom'),
        'prenom' => getarg($_POST, 'prenom'),
        'telephone' => getarg($_POST, 'telephone'),
        'email' => getarg($_POST, 'email'),
        'mdp' => getarg($_POST, 'mdp'),
        'adresse' => getarg($_POST, 'adresse'),
        'denomination' => getarg($_POST, 'denomination'),
        'type' => getarg($_POST, 'type', arg_check(f_is_in(['prive', 'public']))),
    ];
    if ($args['type'] === 'prive') {
        $args['siren'] = getarg($_POST, 'siren');
    }

    $stmt = DB\connect()->prepare('select count(*) from pact._compte where email = ?');
    $stmt->execute([$args['email']]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        html_error('cette adresse e-mail est déjà utilisée.');
    }

    $mdp_hash = password_hash($args['mdp'], PASSWORD_DEFAULT);

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

    if ($args['type'] === 'prive') {
        $stmt = DB\connect()->prepare('insert into pro_prive (email, mdp_hash, nom, prenom, telephone, id_adresse, denomination, siren) values (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $args['email'],
            $mdp_hash,
            $args['nom'],
            $args['prenom'],
            $args['telephone'],
            $idAdresse,
            $args['denomination'],
            str_replace(' ', '', $args['siren']),
        ]);
        redirect_to(location_connexion());
    } else {
        $stmt = DB\connect()->prepare('insert into pro_public (email, mdp_hash, nom, prenom, telephone, id_adresse, denomination) values (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $args['email'],
            $mdp_hash,
            $args['nom'],
            $args['prenom'],
            $args['telephone'],
            $idAdresse,
            $args['denomination'],
        ]);
        redirect_to(location_connexion());
    }
} else {
?>
<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>
        <!-- Section des offres à la une -->
        <h1>Créer un compte professionnel</h1>
        <section class="connexion">
            <div class="champ-connexion">
                <form action="creation_comptePro.php" method="post" enctype="multipart/form-data">
                    <p class="champ">
                        <label>E-mail * <input type="mail" placeholder="exemple@mail.fr" id="email" name="email" required></label>
                    </p>
                    <p class="champ">
                        <label>Mot de passe * <input type="password" placeholder="**********" id="mdp" name="mdp" required></label>
                    </p>
                    <!-- Texte avec label -->
                    <p class="champ">
                        <label>Nom * <input type="text" id="nom" name="nom" placeholder="Breton" required ></label>
                    </p>
                    <p class="champ">
                        <!-- Texte avec label -->
                        <label>Prénom * <input type="text" id="prenom" name="prenom" placeholder="Louis" required ></label>
                    </p>
                    <p class="champ">
                        <!-- Texte avec label -->
                        <label>Téléphone * <input id="telephone" name="telephone" type="tel" placeholder="Format: 0123456789" pattern="[0-9]{10}" maxlength="10" required></label>
                    </p>
                    <p class="champ">
                        <!-- Texte avec label -->
                        <label>Dénomination (raison sociale) * <input type="text" id="denomination" name="denomination" placeholder="Amazon" required ></label>
                    </p>
                    <p class="champ">
                        
                        <label>Adresse * <input type="text" id="adresse" placeholder="22300 1 rue Edouard Branly" name="adresse" ></labe>
                    </p>
                    <p class="radio_entr">
                        <label>Privé <input type="radio" id="prive" name="type" value="prive" onclick="gererAffichage()" ></label>
                        <label>Public <input type="radio" id="public" name="type" value="public" onclick="gererAffichage()" checked></label>
                    </p>
                    <p class="champ" id="champ-siren">
                        <label>SIREN <input type="text" id="siren" name="siren" placeholder="231 654 988" oninput="formatInput(this)" maxlength="12"></label>
                    </p>
                    <p class="error"><?= $_GET['error'] ?? '' ?></p>
                    <button type="submit" class="btn-connexion">Créer un compte professionnel</button>
                </form>
                <br>
                <br>
                <p>Se connecter ?</p>
                <a href="connexion.php">
                    <button class="btn-creer">Se connecter</button>
                </a>
                <br>
            </div>
        </section>
    </main>
    <?php $page->put_footer() ?>

    <script>
    // Fonction pour afficher ou masquer la ligne supplémentaire
    function gererAffichage() {
        // Récupère tous les boutons radio
        let radios = document.querySelectorAll('input[name="type"]');
        let ligneSupplementaire = document.getElementById('champ-siren');
        // Parcourt chaque bouton radio pour voir s'il est sélectionné
        radios.forEach(radio => {
            if (radio.checked && radio.value === 'prive') {
                // Si Option 2 est sélectionnée, on affiche la ligne
                ligneSupplementaire.style.display = 'block';
                ligneSupplementaire.querySelector('input').setAttribute('required', 'required');

            } else if (radio.checked) {
                // Si une autre option est sélectionnée, on masque la ligne
                ligneSupplementaire.style.display = 'none';
                ligneSupplementaire.querySelector('input').removeAttribute('required');
            }
        });
    }
    </script>
    <script>
        // texte siren
        function formatInput(input) {
            // Supprimer tous les espaces
            let value = input.value.replace(/\s/g, '');
            // Limiter à 9 caractères
            if (value.length > 9) {
                value = value.substring(0, 9);
            }
            // Ajouter un espace tous les 3 caractères
            let formattedValue = value.replace(/(.{3})/g, '$1 ').trim();
            input.value = formattedValue;
        }
    </script>
</body>

</html>
<?php
}
?>