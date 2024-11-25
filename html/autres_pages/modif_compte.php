<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'component/offre.php';
require_once 'component/inputs.php';

$page = new Page('Modification compte (todo)');

$args = [
    'id' => getarg($_GET, 'id', arg_filter(FILTER_VALIDATE_INT))
];

$id = $args['id'];
$membre = DB\query_compte_membre($args['id']);
$pro = DB\query_compte_professionnel($args['id']);

// Afficher le détail du compte du membre

if ($_POST) {
    // modif pseudo ------------------------------------------------------------------------------------------------------------------
    $new_pseudo = getarg($_POST, 'new_pseudo', null, required: false);
    if ($new_pseudo) {
        DB\query_uptate_pseudo($id, $new_pseudo);
    }

    // modif denomination ------------------------------------------------------------------------------------------------------------------
    $new_denomination = getarg($_POST, 'new_denomination', null, false);
    if ($new_denomination) {
        DB\query_uptate_denomination($id, $new_denomination);
    }

    // modif siren ------------------------------------------------------------------------------------------------------------------
    $new_siren = getarg($_POST, 'new_siren', null, false);
    if ($new_siren) {
        DB\query_update_siren($id, $new_siren);
    }

    // modif Nom ------------------------------------------------------------------------------------------------------------------
    $new_Nom = getarg($_POST, 'new_nom', null, false);
    if ($new_Nom) {
        DB\query_update_Nom($id, $new_Nom);
    }

    // modif Prenom ------------------------------------------------------------------------------------------------------------------
    $new_Prenom = getarg($_POST, 'new_prenom', null, required: false);
    if ($new_Prenom) {
        DB\query_update_prenom($id, $new_Prenom);
    }

    // modif Email ------------------------------------------------------------------------------------------------------------------
    $new_Email = getarg($_POST, 'new_email', null, false);
    if ($new_Email) {
        DB\query_update_email($id, $new_Email);
    }

    // modif telephone ------------------------------------------------------------------------------------------------------------------
    $new_telephone = getarg($_POST, 'new_telephone', null, false);
    if ($new_telephone) {
        DB\query_update_telephone($id, $new_telephone);
    }

    // modif mot de passe ------------------------------------------------------------------------------------------------------------------
    
    $old_mdp = getarg($_POST, 'old_mdp', null, false);
    
    if($old_mdp){
        $new_mdp = getarg($_POST, 'new_mdp', null, false);
        $confirmation_mdp = getarg($_POST, 'confirmation_mdp', null, false);

        if ($new_mdp && password_verify($old_mdp, $mdp_hash)) {
        if ($confirmation_mdp === $new_mdp) {
            DB\query_uptate_mdp($id, password_hash($new_mdp)); 
        } else {
            redirect_to(location_modif_compte('Mot de passe de confirmation different.'));
        }
        } else {
            redirect_to(location_modif_compte(error: 'Mot de passe incorrect.'));
        }
    }
    
}

$args = [
    'id' => getarg($_GET, 'id', arg_filter(FILTER_VALIDATE_INT))
];



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
} else {
    html_error("le compte d'ID {$args['id']} n'existe pas");
}

?>
<!DOCTYPE html>
<html lang="en">
<?php $page->put_head() ?>
<body>
<?php $page->put_header() ?>
<main>

<section id="info_compte">  
    <form action="modif_compte.php?id=<?= $id ?>" method="POST">


        <a href="/autres_pages/detail_compte.php?id=<?= $id ?>">retour</a>
        <?php if ($membre !== false) { ?>
            <div>
                <div id="pseudo">
                    <label>Pseudo : </label>
                    <?= $pseudo ?>
                </div>
                <input id="new_pseudo" name="new_pseudo" type="text" placeholder="votre nouveau pseudo">
            </div>
        <?php } else if ($pro !== false) { ?>
            <div>
                <div id="denomination">
                    <label>Denomination : </label>
                    <?= $denomination ?>
                </div>
                <input id="new_denomination" name="new_denomination" type="text" placeholder="votre nouvelle denomination">
            </div>
            <?php
            if (DB\exists_pro_prive($id)) {
                ?>
                    <div>
                    <div id="siren">
                    <label>siren : </label>
                <?php
                echo $siren
                ?> </div>
                    <input type="text" id="new_siren" name="new_siren" placeholder="231 654 988" oninput="formatInput(this)" maxlength="12">
            </div><?php
            }
            ?>


        <?php } ?>


        <div>
            <div>
                <label>Nom : </label>
                <?= $nom ?>
            </div>
            <input id="new_nom" name="new_nom" type="text" placeholder="votre nouveau nom">
        </div>

        <div>
            <div>
                <label>Prenom : </label>
                <?= $prenom ?>
            </div>
            <input id="new_prenom" name="new_prenom" type="text" placeholder="votre nouveau prenom">
        </div>

        <div>
            <div>
                <label>Email : </label>
                <?= $email ?>
            </div>
            <input id="new_email" name="new_email" type="new_email" placeholder="votre nouvel email">

        </div>
        <div></div>
            <div id="telephone">
                <label>Numero telephone : </label>
                <?= $telephone ?>
            </div>
            <input id="new_telephone" name="new_telephone" type="tel" placeholder="votre nouveau numero telephone">

        </div>

        <div>
        <div id="adresse">
                <p>adresse : </p>
                <?= format_adresse($adresse) ?>
        </div>
            <?php put_input_address('', 'adresse', 'adresse_') ?>

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
            <button type="submit">valider</button>
        </div>

       


    </form>
</section>

</main>
<?php $page->put_footer() ?>
</body>
</html>
