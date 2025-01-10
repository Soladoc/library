<?php

require_once 'util.php';
require_once 'db.php';
require_once 'model/Adresse.php';
require_once 'model/MultiRange.php';
require_once 'model/FiniteTimestamp.php';

header('Content-Type: application/json; charset=utf-8');

$stmt = notfalse(DB\connect()->prepare("select o.*, coalesce((select json_agg(t.tag) from _tags t where t.id_offre = o.id), json '[]') tags from offres o"));
notfalse($stmt->execute());
$offres = $stmt->fetchAll();

foreach ($offres as &$o) {
    $o['prix_min']          = floatval($o['prix_min']);
    $o['formatted_address'] = Adresse::from_db($o['id_adresse'])->format();
    apply($o['tags'], json_decode(...));
    apply($o['option'], json_decode(...));
    apply($o['periodes_ouverture'], fn($p) => MultiRange::parse($p, FiniteTimestamp::parse(...))->ranges);
}

echo notfalse(json_encode($offres));
