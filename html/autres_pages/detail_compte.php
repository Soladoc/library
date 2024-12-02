<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'redirect.php';
require_once 'component/Page.php';

$args = [
    'id' => getarg($_GET, 'id', arg_int())
];

$page = new Page("detail_compte_membre : {$args['id']}",
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'],
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' => 'async']);


$id = $args['id'];
$membre = DB\query_compte_membre($args['id']);
$pro = DB\query_compte_professionnel($args['id']);

if ($membre !== false) {
    // echo '<pre>';
    // print_r($membre);
    // echo '</pre>';
    $pseudo = $membre['pseudo'];
    $email = $membre['email'];
    $mdp = unserialize($membre['mdp_hash']);
    $nom = $membre['nom'];
    $prenom = $membre['prenom'];
    $telephone = $membre['telephone'];
    $id_adresse = $membre['id_adresse'];
    $adresse = DB\query_adresse($id_adresse);
} else if ($pro !== false) {
    // echo '<pre>';
    // print_r($pro);
    // echo '</pre>';
    $denomination = $pro['denomination'];
    $email = $pro['email'];
    $mdp_hash = unserialize($pro['mdp_hash']);
    $nom = $pro['nom'];
    $prenom = $pro['prenom'];
    $telephone = $pro['telephone'];
    $id_adresse = $pro['id_adresse'];
    $adresse = DB\query_adresse($id_adresse);

    if (DB\exists_pro_prive($id)) {
        $siren = DB\query_get_siren($id);
    }
} else {
    html_error("le compte d'ID {$args['id']} n'existe pas");
}

?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
    <?php $page->put_header() ?>

    <main>
        <section id="info_compte">
            <?php
            if ($membre !== false) {
                ?>
            <div id="pseudo">
                <p>Pseudo : </p>
                <?= $pseudo ?>
            </div>
            <?php } else if ($pro !== false) { ?>
                <div id="denomination">
                <p>Denomination : </p>
                <?php
                echo $denomination
                ?> </div>

                

                <?php

                if (DB\exists_pro_prive($id)) {
                    ?>
                    <div id="siren">
                    <p>Siren : </p>
                <?php
                echo $siren
                ?> </div><?php
                }
                ?>
                
           


            <?php } ?>

            <label for="nom">Nom :*</label>
                <p>
                    <input form="f" id="nom" name="nom" type="text" value="<?= htmlspecialchars($nom) ?>" required>
                </p>

            <div id="prenom">
                <p>Prenom : </p>
                <?= $prenom ?>
            </div>

            <div id="email">
                <p>Email : </p>
                <?= $email ?>
            </div>

            <div id="telephone">
                <p>Numero de telephone : </p>
                <?= $telephone ?>
            </div>

            <div id="adresse">
                <p>adresse : </p>
                <?= format_adresse($adresse) ?> </div>
        <a href="modif_compte.php?id=<?= $id ?>">modifier</a>
            <?php ?>
            
           
        </section>

    </main>

    <?php $page->put_footer() ?>

</body>

</html>