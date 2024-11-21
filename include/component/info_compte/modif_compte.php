<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'component/head.php';
require_once 'component/offre.php';
require_once 'component/inputs.php';



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
        $adresse= query_adresse($id_adresse);
}
else if ($pro !== false) {
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
        $adresse= query_adresse($id_adresse);
    
}
else {
    html_error("le compte d'ID {$args['id']} n'existe pas");
}
// Afficher le détail du compte du membre


if ($_POST) {
    $new_mdp = getarg($_POST, 'new_mdp');
    $confirmation_mdp = getarg($_POST, 'confirmation_mdp');
    $old_mdp = getarg($_POST, 'old_mdp');

    if (password_verify($old_mdp, $mdp_hash)) {
        if ($confirmation_mdp === $new_mdp ) {
            uptate_mdp($id,$new_mdp);
        }
        else{
            header('Location: /autres_pages/connexion.php?error_confirmation=' . urlencode("Mot de passe de confirmation different."));

        }
    }
    else {
        header('Location: /autres_pages/connexion.php?error_mdp=' . urlencode(" Mot de passe incorrect."));

    }
    
}

?>

<section id="info_compte">  
    <form action="modif_compte.php" method="POST">


        <a href="/autres_pages/detail_compte.php?id=<?php echo $id ?>">retour</a>
        <?php if ($membre !== false) {?>
            <div>
                <div id="pseudo">
                    <label>Pseudo : </label>
                    <?php echo $pseudo ?>
                </div>
                <input id="new_pseudo" name="pseudo" type="text" placeholder="votre nouveau pseudo">
            </div>
        <?php }

        else if ($pro !== false){ ?>
            <div>
                <div id="denomination">
                    <label>Denomination : </label>
                    <?php echo $denomination ?>
                </div>
                <input id="new_denomination" name="denomination" type="text" placeholder="votre nouvelle denomination">
            </div>
            <?php 
            if (exists_pro_prive($id)) {
                    ?>
                    <div>
                    <div id="siren">
                    <label>siren : </label>
                <?php echo $siren 
                ?> </div>
                    <input type="text" id="siren" name="siren" placeholder="231 654 988" oninput="formatInput(this)" maxlength="12">
            </div><?php
                    
                   
                }?>


        <?php } ?>


        <div>
            <div>
                <label>Nom : </label>
                <?php echo $nom ?>
            </div>
            <input id="new_nom" name="nom" type="text" placeholder="votre nouveau nom">
        </div>

        <div>
            <div>
                <label>Prenom : </label>
                <?php echo $prenom ?>
            </div>
            <input id="new_prenom" name="prenom" type="text" placeholder="votre nouveau prenom">
        </div>

        <div>
            <div>
                <label>Email : </label>
                <?php echo $email ?>
            </div>
            <input id="new_email" name="email" type="email" placeholder="votre nouvel email">

        </div>
        <div></div>
            <div id="telephone">
                <label>Numero telephone : </label>
                <?php echo $telephone ?>
            </div>
            <input id="new_telephone" name="telephone" type="tel" placeholder="votre nouveau numero telephone">

        </div>

        <div>
        <div id="adresse">
                <p>adresse : </p>
                <?php echo format_adresse($adresse);  
                ?> </div>
            <?php put_input_address("",'adresse', 'adresse_');?>

        </div>




        <div id='changer_mdp'>
            <label>modifier son mot de passe</label>                        
            <div class="champ">
                <label for="mdp">Mot de passe actuel *</label>
                <input id="mdp" name="old_mdp" type="password" placeholder="**********">
            </div>
            <div class="champ">
                <label for="mdp">Nouveau mot de passe *</label>
                <input id="new_mdp" name="mdp" type="password" placeholder="**********">
            </div>
            <div class="champ">
                <label for="mdp">confirmation mot de passe *</label>
                <input id="confirmation_mdp" name="mdp" type="password" placeholder="**********">
            </div>
            <?php if ($error = $_GET['error_mdp'] ?? null) { ?>
            <p class="error"><?= $error ?></p>
            <?php } ?>
            <?php if ($error = $_GET['error_confirmation'] ?? null) { ?>
            <p class="error"><?= $error ?></p>
            <?php } ?>
            <button type="submit" class="btn-connexion">valider</button>
        </div>

       


    </form>
</section>

            







