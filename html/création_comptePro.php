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
                    <input type="text" placeholder="exemple@mail.fr" required>
                </div>
                <br>
                <div class="champ">
                    <p>Mot de passe *</p>
                    <input type="text" placeholder="**********" required>
                </div>
                <br>
                <!-- Texte avec label -->
                <div class="champ">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" placeholder="Nyx" required />
                </div>
                <br />
                <div class="champ">
                    <!-- Texte avec label -->
                    <label for="prenom">PreNom :</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Icelos" required />
                </div>
                <br />
                <div class="champ">
                    <!-- Texte avec label -->
                    <label for="telephone">Telephone :</label>
                    <input type="text" id="telephone"nametelephone" placeholder="00 00 00 00 00" required />
                    </div>
                <br />
                <div class="champ">
                    <!-- Texte avec label -->
                    <label for="denomination">Denomination (raison social)  *:</label>
                    <input type="text" id="denomination" name="denomination" placeholder="Panthéon de la nuit étoilée"  required />
                </div>
                <br />
                <div class="champ">
                    <!-- Email -->
                    <label for="adresse">adresse *:</label>
                    <input type="text" id="adresse" placeholder="1 rue che 22050 truc" name="adresse" />
                </div>
                <br />

                <div class="radio_entr">
                <div>
                    <input type="radio" id="public" name="drone" value="huey" checked />
                    <label for="public">Public</label>
                </div>

                <div>
                    <input type="radio" id="prive" name="drone" value="prive" />
                    <label for="prive">Privé</label>
                </div>
                </div>
               
                <br>
                <div class="champ">
                    <!-- Texte avec label -->
                    <label for="siren">SIREN*:</label>
                    <input type="text" id="siren" name="siren" placeholder="231 654 987     12315" required />
                </div>
                <br>
                <button class="btn-connexion">Créer un compte personnel</button>
                
                <br /><br>
                <p>Se connecter ?</p>
                <button class="btn-creer">Se connecter</button>
                
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
