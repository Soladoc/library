<?php
require_once 'testing.php';
require_once 'component/InputOffre.php';

// Test create offer

// Parse HTML output of inputoffre

const ID_PRO = 1;

Auth\se_connecter_pro(ID_PRO);

$pro = Professionnel::from_db(ID_PRO);

$input_offre = new InputOffre(Activite::CATEGORIE, $pro);

notfalse(ob_start());
$input_offre->put();
$create_offre_html = notfalse(ob_get_clean());

$doc = new DOMDocument();
notfalse($doc->loadHTML($create_offre_html));

dbg_print($doc);
