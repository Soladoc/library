<?php

require_once 'model/Reponse.php';
require_once 'redirect.php';

$id_avis = getarg($_GET, 'id_avis', arg_int());
$contenu = getarg($_POST, 'contenu');
$reponse = Reponse::from_db_by_avis($id_avis);

if ($reponse === null) {
    $reponse = new Reponse(null, $id_avis, $contenu);
} else {
    $reponse->contenu = $contenu;
}

$reponse->push_to_db();

redirect_to(getarg($_GET, 'return_url'));
