<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'component/offre.php';

$page = new Page("detail_compte_membre : {$args['id']}",
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'],
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' => 'async']);

$args = [
    'id' => getarg($_GET, 'id', arg_filter(FILTER_VALIDATE_INT))
];
$id = $args['id'];
$membre = query_compte_membre($args['id']);
$pro = query_compte_professionnel($args['id']);

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
    $adresse = query_adresse($id_adresse);
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
    $adresse = query_adresse($id_adresse);

    if (exists_pro_prive($id)) {
        $siren = query_get_siren($id);
    }
} else {
    html_error("le compte d'ID {$args['id']} n'existe pas");
}
// Afficher le détail du compte du membre

if ($_POST) {
    $new_mdp = getarg($_POST, 'new_mdp');
    $confirmation_mdp = getarg($_POST, 'confirmation_mdp');
    $old_mdp = getarg($_POST, 'old_mdp');

    if ($new_mdp && password_verify($old_mdp, $mdp_hash)) {
        if ($confirmation_mdp === $new_mdp) {
            update_mdp($id, $new_mdp);  // todo: cette fonction n'exite pas
        } else {
            redirect_to(location_connexion('Mot de passe de confirmation different.'));
        }
    } else {
        redirect_to(location_connexion(error: 'Mot de passe incorrect.'));
    }
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

                if (exists_pro_prive($id)) {
                    ?>
                    <div id="siren">
                    <p>siren : </p>
                <?php
                echo $siren
                ?> </div><?php
                }
                ?>
                
           


            <?php } ?>

            <div id="nom">
                <p>Nom : </p>
                <?= $nom ?>
            </div>

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
                <?php
                echo format_adresse($adresse);
                ?> </div>
        <a href="modif_compte.php?id=<?= $id ?>">modifier</a>
            <?php ?>
            
           
        </section>

    </main>

    <?php $page->put_footer() ?>

</body>

</html>