<?php
require_once 'util.php';
require_once 'queries.php';
header('Content-Type: application/json; charset=utf-8');

$offres = [];

foreach (iterator_to_array(DB\query_images(), false) as $row) {
    $offres[array_pop_key($row, 'id')] = $row;
}
echo notfalse(json_encode($offres));