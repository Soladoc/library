<?php
require_once 'queries.php';
header('Content-Type: application/json; charset=utf-8');

$offres = iterator_to_array(DB\query_images(), false);
echo notfalse(json_encode($offres));