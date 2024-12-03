<?php

use function Auth\exiger_connecte_pro;

require_once 'queries.php';
require_once 'auth.php';
require_once 'util.php';
require_once 'const.php';
require_once 'component/inputs.php';
require_once 'component/Page.php';
require_once 'component/InputOffre.php';

$page = new Page("Création d'une offre",
    ['creation_offre.css'],
    ['module/creation_offre.js' => 'defer type="module"']);

$categorie = getarg($_GET, 'type_offre', arg_check(f_is_in(array_keys(CATEGORIES_OFFRE))));

$input_offre = new InputOffre(
    $categorie,
    Professionnel::from_db(exiger_connecte_pro()),
    form_id: 'f',
);

if ($_POST) {
    $a = $input_offre->get($_POST);
    // Délégation du traitement à un autre script pour gagner de la place
    require 'traitement/creation_offre.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>
    <main>
        <?php $input_offre->put() ?>

        <form id="<?= $input_offre->form_id ?>" method="post" enctype="multipart/form-data">
            <button type="submit">Valider</button>
        </form>
    </main>
    <?php $page->put_footer() ?>
</body>

</html>
<?php
