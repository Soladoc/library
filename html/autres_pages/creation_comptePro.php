<?php

require_once 'db.php';
if (isset($_POST['mdp'])) {
    echo "ici on est bien";
    print 'Votre nom :' . $_POST['nom'];
    print 'Votre prenom :' . $_POST['prenom'];
    print 'Votre numero de telephone :' . $_POST['telephone'];
    print 'Votre mail :' . $_POST['email'];
    print 'Votre mot de passe :' . $_POST['mdp'];
    print 'Votre adresse :' . $_POST['adresse'];

    $estprive = isset($_POST['type']); 
    $mdp_hash = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

    $pdo = db_connect();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM pact._compte WHERE email = :email');
    $stmt->execute(['email' => $_POST['email']]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        echo 'Cette adresse e-mail est déjà utilisée.';
        exit;
    }


    if ($estprive) {
        echo 'oeoeeo';
        // insert in pro_prive
        $sql = 'INSERT INTO  pact.pro_prive (email, mdp_hash, nom, prenom, telephone, denomination, siren) VALUES (:email, :mdp_hash, :nom, :prenom, :telephone, :denomination, :siren)';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':mdp_hash', $mdp_hash);
        $stmt->bindParam(':nom', $_POST['nom']);
        $stmt->bindParam(':prenom', $_POST['prenom']);
        $stmt->bindParam(':telephone', $_POST['telephone']);
        $stmt->bindParam(':denomination', $_POST['denomination']);
        $stmt->bindParam(':siren', $_POST['siren']);

        // 3. Exécuter la requête avec les valeurs
        $stmt->execute([
            ':email' => $_POST['email'],
            ':mdp_hash' => $mdp_hash,
            ':nom' => $_POST['nom'],
            ':prenom' => $_POST['prenom'],
            ':telephone' => $_POST['telephone'],
            ':denomination' => $_POST['denomination'],
            ':siren' => $_POST['siren']
        ]);

        echo "<script>window.location.href='../autres_pages/connexion.php';</script>";
    } else {
        // insert in pro_public
        $sql = 'INSERT INTO  pact.pro_public (email, mdp_hash, nom, prenom, telephone, denomination) VALUES (:email, :mdp_hash, :nom, :prenom, :telephone, :denomination)';
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':mdp_hash', $mdp_hash);
        $stmt->bindParam(':nom', $_POST['nom']);
        $stmt->bindParam(':prenom', $_POST['prenom']);
        $stmt->bindParam(':telephone', $_POST['telephone']);
        $stmt->bindParam(':denomination', $_POST['denomination']);

        // 3. Exécuter la requête avec les valeurs
        $stmt->execute([
            ':email' => $_POST['email'],
            ':mdp_hash' => $mdp_hash,
            ':nom' => $_POST['nom'],
            ':prenom' => $_POST['prenom'],
            ':telephone' => $_POST['telephone'],
            ':denomination' => $_POST['denomination']
        ]);
        echo "<script>window.location.href='../autres_pages/connexion.php';</script>";
    }
    echo "okkkkkkk";
    echo "<script>window.location.href='../autres_pages/accPro.php';</script>";
} else {
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
    <?php require 'component/header.php' ?>
    <main>
        <!-- Section des offres à la une -->
        <h1>Créer un compte professionnel</h1>
        <section class="connexion">
            <div class="champ-connexion">
                <form action="creation_comptePro.php" method="post" enctype="multipart/form-data">

                    <br>
                    <div class="champ">
                        <p>E-mail *</p>
                        <input type="mail" placeholder="exemple@mail.fr" id="email" name="email" required>
                    </div>
                    <br>
                    <div class="champ">
                        <p>Mot de passe *</p>
                        <input type="password" placeholder="**********" id="mdp" name="mdp" required>
                    </div>
                    <br>
                    <!-- Texte avec label -->
                    <div class="champ">
                        <p>Nom :</p>
                        <input type="text" id="nom" name="nom" placeholder="Breton" required />
                    </div>
                    <br />
                    <div class="champ">
                        <!-- Texte avec label -->
                        <p>Prenom :</p>
                        <input type="text" id="prenom" name="prenom" placeholder="Louis" required />
                    </div>
                    <br />
                    <div class="champ">
                        <!-- Texte avec label -->
                        <p>Téléphone :</p>
                        <input id="telephone" name="telephone" type="tel" placeholder="Format: 0123456789" pattern="[0-9]{10}" required>
                    </div>
                    <br />
                    <div class="champ">
                        <!-- Texte avec label -->
                        <p>Dénomination (raison sociale) *:</p>
                        <input type="text" id="denomination" name="denomination" placeholder="Amazon" required />
                    </div>
                    <br />
                    <div class="champ">
                        <!-- Email -->
                        <p>Adresse *:</p>
                        <input type="text" id="adresse" placeholder="22300 1 rue Edouard Branly" name="adresse" />
                    </div>
                    <br />

                    <div class="radio_entr">
                        <div>
                            <input type="radio" id="prive" name="type" value="prive" onclick="gererAffichage()" checked />
                            <label for="prive" style="font-family:'Tw Cen MT'">Privé</label>
                        </div>
                        <div>
                            <input type="radio" id="public" name="type" value="public" onclick="gererAffichage()" />
                            <label for="public" style="font-family:'Tw Cen MT'">Public</label>
                        </div>
                    </div>

                    <br>
                    <div class="champ" id="siren">
                        <label>SIREN*:</label>
                        <input type="text" id="siren" name="siren" placeholder="231 654 987" maxlength="9" pattern="\d{9}" title="Veuillez entrer un SIREN valide de 9 chiffres" />
                    </div>
                    <br>
                    <button type="submit" class="btn-connexion">Créer un compte professionnel</button>
                </form>
                <br /><br>
                <p>Se connecter ?</p>
                <a href="connexion.php">
                    <button class="btn-creer">Se connecter</button>
                </a>
                <br>
            </div>
        </section>
    </main>
    <?php require 'component/footer.php' ?>

    <script>
    // Fonction pour afficher ou masquer la ligne supplémentaire
    function gererAffichage() {
        // Récupère tous les boutons radio
        let radios = document.querySelectorAll('input[name="type"]');
        let ligneSupplementaire = document.getElementById("siren");
        // Parcourt chaque bouton radio pour voir s'il est sélectionné
        radios.forEach(radio => {
            if (radio.checked && radio.value === 'prive') {
                // Si Option 2 est sélectionnée, on affiche la ligne
                ligneSupplementaire.style.display = 'block';
                ligneSupplementaire.setAttribute('required','required');

            } else if (radio.checked) {
                // Si une autre option est sélectionnée, on masque la ligne
                ligneSupplementaire.style.display = 'none';
                ligneSupplementaire.removeAttribute('required');
            }
        });
    }
    </script>
</body>

</html>
<?php
}
?>