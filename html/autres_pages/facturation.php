<?php
require_once 'component/Page.php';
require_once 'auth.php';
require_once 'queries/offre.php';
require_once 'model/Duree.php';

$page = new Page('Facturation');

$page->put(function () {
    $resG = 0;//resultat global
    $resO = 0;//resultat offre
    $id_professionnel = Auth\exiger_connecte_pro();
    $offres = DB\query_offres($id_professionnel);
    foreach ($offres as $offre) {
        echo '<pre>';
        print_r($offre['titre'].' | ' );
        print_r($offre['libelle_abonnement'].' | ' );
        print_r($offre['categorie'].' | ' );
        print_r(Duree::parse($offre['en_ligne_ce_mois_pendant'])->days.' | ' );
        ;
        $resO = Duree::parse($offre['en_ligne_ce_mois_pendant'])->days * 1;
        print_r($resO);
        echo '</br';
        echo '</pre>';
    }
});