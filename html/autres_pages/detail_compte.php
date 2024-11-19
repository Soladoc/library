<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'component/head.php';

$args = [
    'id' => getarg($_GET, 'id', arg_filter(FILTER_VALIDATE_INT))
];


$membre = query_compte_membre($args['id']);
$pro = query_compte_professionnel($args['id']);

if ($membre !== false) {
    echo '<pre>';
    print_r($membre);
    echo '</pre>';
    $pseudo = $membre['pseudo'];
    $email = $membre['email'];
    $mdp = unserialize($membre['mdp_hash']);
    $nom = $membre['nom'];
    $prenom = $membre['prenom'];
    $telephone = $membre['telephone'];
}
else if ($pro !== false) {
    echo '<pre>';
    print_r($pro);
    echo '</pre>';
    $denomination = $pro['denomination'];
    $email = $pro['email'];
    $mdp = unserialize($pro['mdp_hash']);
    $nom = $pro['nom'];
    $prenom = $pro['prenom'];
    $telephone = $pro['telephone'];
    
}
else {
    html_error("le compte d'ID {$args['id']} n'existe pas");
}
// Afficher le détail du compte du membre



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
            <?php if ($membre !== false) {
                
            ?>
            <div id="pseudo">
                <p>pseudo : </p>
                <?php echo $pseudo ?>
            </div>
            <?php }
            else if ($pro !== false){ ?>
                <div id="denomination">
                <p>denomination : </p>
                <?php echo $denomination ?>
            </div>


            <?php } ?>

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

            <div id="telephone">
                <p>telephone : </p>
                <?php echo $telephone ?>
            </div>

            <div id='changer_mdp'>
                <form action="detail_compte" method="POST">
                    <p>modifier son mot de passe</p>
                    <div class="champ">
                        <label for="mdp">Mot de passe *</label>
                        <input id="mdp" name="mdp" type="password" placeholder="**********" required>
                    </div>
                    <?php if ($error = $_GET['error'] ?? null) { ?>
                    <p class="error"><?= $error ?></p>
                    <?php } ?>
                    <button type="submit" class="btn-connexion">valider</button>
                </form>

            </div>
           
        </section>

    </main>

    <?php require 'component/footer.php' ?>

</body>

</html>