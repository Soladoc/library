<?php
require_once 'queries.php';
header('Content-Type: application/json; charset=utf-8');

$offres = iterator_to_array(DB\query_offres(), false);
foreach ($offres as &$offre) {
    $offre['prix_min'] = floatval($offre['prix_min']);
    $offre['formatted_address'] = format_adresse(notfalse(DB\query_adresse($offre['id_adresse'])));
    $offre['tags'] = DB\query_tags($offre['id']);
}
echo notfalse(json_encode($offres));
?>