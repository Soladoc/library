<?php
require_once 'util.php';

$id_compte = getarg($_GET, 'id_compte', arg_int());
$id_signalable = getarg($_GET, 'id_signalable', arg_int());
$raison = getarg($_GET, 'raison');
$return_url = getarg($_GET, 'return_url');

Signalable::from_db($id_signalable)->signaler($id_compte, $raison);

redirect_to($return_url);