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

    <main id="detail_compte" >
        <section id="info_compte">
            <!-- PHP dynamique commence ici -->
            <?php if ($membre !== false): ?>
                <div id="pseudo">
                    <p>Pseudo :</p>
                    <span><?= $pseudo ?></span>
                </div>
            <?php elseif ($pro !== false): ?>
                <div id="denomination">
                    <p>Dénomination :</p>
                    <span><?= $denomination ?></span>
                </div>
                <?php if (DB\exists_pro_prive($id)): ?>
                    <div id="siren">
                        <p>Siren :</p>
                        <span><?= $siren ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div id="nom">
                <p>Nom :</p>
                <span><?= $nom ?></span>
            </div>

            <div id="prenom">
                <p>Prénom :</p>
                <span><?= $prenom ?></span>
            </div>

            <div id="email">
                <p>Email :</p>
                <span><?= $email ?></span>
            </div>

            <div id="telephone">
                <p>Numéro de téléphone :</p>
                <span><?= $telephone ?></span>
            </div>

            <div id="adresse">
                <p>Adresse :</p>
                <span><?= format_adresse($adresse) ?></span>
            </div>

            <button><a href="modif_compte.php?id=<?= $id ?>">Modifier</a></button>
        </section>
    </main>

    <?php $page->put_footer() ?>

</body>
</html>
