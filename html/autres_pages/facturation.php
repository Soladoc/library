<?php 
session_start();
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
            $OFFRES = query_offres($id_professionnel);
            while ($offer = $OFFRES->fetch()) {
                echo "<pre>";
                print_r($offer['libelle_abonnement']);
                echo "</pre>";
            }
            $OFFRES->closeCursor()
        ?>
    </main>
</body>
</html>