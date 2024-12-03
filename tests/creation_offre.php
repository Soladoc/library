<?php
require_once 'testing.php';
require_once 'component/InputOffre.php';

// Test creation_offre

// Parse HTML output of inputoffre

const ID_PRO = 1;
const FORM_ID = 'f';

Auth\se_connecter_pro(ID_PRO);

$pro = Professionnel::from_db(ID_PRO);

$input_offre = new InputOffre(Activite::CATEGORIE, $pro);

notfalse(ob_start());
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test creation_offre</title>
</head>
<body>
<form id="<?= FORM_ID ?>">
    <?php $input_offre->put(); ?>
</form>
</body>
</html>
<?php
$create_offre_html = notfalse(ob_get_clean());

$dom = new IvoPetkov\HTML5DOMDocument();
notfalse($dom->loadHTML($create_offre_html));

fill_input($dom, , )

$request = submit_form($dom, FORM_ID);
