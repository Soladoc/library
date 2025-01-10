<?php

use function DB\query_tarif;

require_once 'component/Page.php';
require_once 'auth.php';
require_once 'queries/offre.php';
require_once 'model/Duree.php';
require_once 'queries.php';

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
        print_r(query_tarif($offre['libelle_abonnement']).' | ');
        print_r($offre['categorie'].' | ' );
        print_r(Duree::parse($offre['en_ligne_ce_mois_pendant'])->days.' | ' );
        $resO = Duree::parse($offre['en_ligne_ce_mois_pendantc '])->days * query_tarif($offre['libelle_abonnement']);// le 1 sera le prix de l'abonnement a terme.
        $resO += $resO * 0.20;
        $resG += $resO; 
        print_r($resO.' € TTC');
        echo '</br';
        echo '</pre>';
    }
    echo '<pre>';
    print_r('Prix global '.$resG.' € TTC');
    echo '</pre>';
});