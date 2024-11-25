<?php
require_once 'component/offre.php';
require_once 'component/Page.php';
require_once 'auth.php';

$page = new Page('Facturation');

$id_professionnel = exiger_connecte_pro();
?>


<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>
        <?php
        $offres = query_offres($id_professionnel);
        while ($offre = $offres->fetch()) {
            echo '<pre>';
            print_r($offre['libelle_abonnement']);
            // cc benjamin, j'ai renommé l'attribut prix de abonnement en prix_jouranlier
            // ce message s'autodétruira dans 5 réinitialisations de BDD
            echo '</pre>';
        }
        $offres->closeCursor()
        ?>
    </main>
</body>
</html>