<?php
require_once 'component/Page.php';
require_once 'auth.php';

$page = new Page('Facturation');

$id_professionnel = Auth\exiger_connecte_pro();
?>


<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>
        <?php
        $offres = DB\query_offres($id_professionnel);
        foreach ($offres as $offre) {
            echo '<pre>';
            print_r($offre['titre']);
            print_r($offre['libelle_abonnement']);
            print_r($offre['categorie']);
            print_r($offre['en_ligne_ce_mois_pendant']);
            // cc benjamin, j'ai renommé l'attribut prix de abonnement en prix_jouranlier
            // ce message s'autodétruira dans 5 réinitialisations de BDD
            echo '</pre>';
        }
        ?>
    </main>
</body>
</html>