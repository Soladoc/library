<?php
require_once 'component/Page.php';
require_once 'auth.php';
require_once 'queries/offre.php';

$page = new Page('Facturation');

$page->put(function () {
    $id_professionnel = Auth\exiger_connecte_pro();
    $offres = DB\query_offres($id_professionnel);
    foreach ($offres as $offre) {
        echo '<pre>';
        print_r($offre['titre'].' | ' );
        print_r($offre['libelle_abonnement'].' | ' );
        print_r($offre['categorie'].' | ' );
        print_r($offre['en_ligne_ce_mois_pendant'].' | ' );
        // cc benjamin, j'ai renommé l'attribut prix de abonnement en prix_jouranlier
        // ce message s'autodétruira dans 5 réinitialisations de BDD
        echo '</br';
        echo '</pre>';
    }
});