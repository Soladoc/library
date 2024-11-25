<?php
require_once 'queries.php';
header('Content-Type: application/json; charset=utf-8');

$offres = iterator_to_array(DB\query_offres(), false);
$i=0;
foreach ($offres as &$offre) {
    $offre['min'] = floatval($offre['min']);
    $offre['formatted_address'] = format_adresse(notfalse(DB\query_adresse($offre['id_adresse'])));
    error_log(strval($i++));
}
echo notfalse(json_encode($offres));