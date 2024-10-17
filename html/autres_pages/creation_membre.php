<?php
if (isset($_POST['motdepasse'])) {
    print 'Votre pseudo :'.$_POST['pseudo'];
    print 'Votre nom :'.$_POST['nom'];
    print 'Votre prenom :'.$_POST['prenom'];
    print 'Votre numero de telephone :'.$_POST['telephone'];
    print 'Votre mail :'.$_POST['email'];
    print 'Votre mot de passe :'.$_POST['motdepasse'];à
    lmù

}
else {
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Création de compte</title>
   </head>
   <body>
      <form action="creation_membre.php" method="post" enctype="multipart/form-data">
         <!-- Texte avec label -->
         <label for="pseudo">Pseudo :</label>
         <input type="text" id="pseudo" name="pseudo" required />
         <br />

         <label for="nom">Nom :</label>
         <input type="text" id="nom" name="nom" required />
         <br />

         <label for="prenom">Prenom :</label>
         <input type="text" id="prenom" name="prenom" required />
         <br />

         <label for="telephone">Téléphone :</label>
         <input type="text" id="telephone" name="telephone" required />
         <br />

         <label for="email">Email :</label>
         <input type="mail" id="email" name="email" required />
         <br />

         <label for="motdepasse">Mot de passe :</label>
         <input type="password" id="motdepasse" name="motdepasse" required />

         <!-- Bouton de soumission -->
         <input type="submit" value="Rechercher" />
      </form>
   </body>
</html>
<?php
}
?>
