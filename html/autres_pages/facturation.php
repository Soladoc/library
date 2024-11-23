<?php 
require_once 'component/offre.php';
require_once 'component/head.php';
require_once 'auth.php';

$id_professionnel = exiger_connecte_pro();
?>


<!DOCTYPE html>
<html lang="fr">

<?php put_head("Facturation") ?>

<body>
    <?php require 'component/header.php' ?>
    <main>
        <?php
            $offres = query_offres($id_professionnel);
            while ($offre = $offres->fetch()) {
                echo "<pre>";
                print_r($offre['libelle_abonnement']);
                // cc benjamin, j'ai renommé l'attribut prix de abonnement en prix_jouranlier
                // ce message s'autodétruira dans 5 réinitialisations de BDD
                echo "</pre>";
            }
            $offres->closeCursor()
        ?>
    </main>
</body>
</html>