<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'component/head.php';

$args = [
    'id' => getarg($_GET, 'id', arg_filter(FILTER_VALIDATE_INT))
];

$membre = query_compte_membre($args['id']);

if ($membre === false) {
    html_error("l'membre d'ID {$args['id']} n'existe pas");
}
// Afficher le détail du compte du membre

echo '<pre>';
print_r($membre);
echo '</pre>';
$pseudo = $membre['pseudo'];
$email = $membre['email'];
$mdp = unserialize($membre['mdp_hash']);
$nom = $membre['nom'];
$prenom = $membre['prenom'];
$telephone = $membre['telephone'];

?>

<!DOCTYPE html>
<html lang="fr">

<?php put_head("detail_compte_membre : {$args['id']}",
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'],
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' => 'async']) ?>

<body>
    <?php

    require 'component/header.php'
    ?>

    <main>
        <section id="info_compte">
            <div id="pseudo">
                <p>pseudo : </p>
                <?php echo $pseudo ?>
            </div>

            <div id="nom">
                <p>nom : </p>
                <?php echo $nom ?>
            </div>

            <div id="prenom">
                <p>prenom : </p>
                <?php echo $prenom ?>
            </div>

            <div id="email">
                <p>email : </p>
                <?php echo $email ?>
            </div>

            <div id="mdp">
                <p>mot de passe : </p>
                <?php echo $mdp ?>
            </div>

        </section>

    </main>

    <?php require 'component/footer.php' ?>

</body>

</html>