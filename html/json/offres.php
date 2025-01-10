<?php

require_once 'queries.php';
//require_once 'model/MultiRange.php';
header('Content-Type: application/json; charset=utf-8');

$stmt = notfalse(DB\connect()->prepare('select * from offres'));
DB\bind_values($stmt, $args);
notfalse($stmt->execute());
$offres = $stmt->fetchAll();

foreach ($offres as &$offre) {
    $offre['prix_min'] = floatval($offre['prix_min']);
    $offre['formatted_address'] = format_adresse(notfalse(DB\query_adresse($offre['id_adresse'])));
    $offre['tags'] = DB\query_tags($offre['id']);
    //$offre['periodes_ouverture']=MultiRange::parse($offre['periodes_ouverture'],FiniteTimestamp::parse(...));
}

echo notfalse(json_encode($offres));
