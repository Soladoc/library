<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'component/inputs.php';

$page = new Page('Modification compte (todo)');
$error_mdp = null;
$error_tel = null;
$error_email = null;

$args = [
    'id' => getarg($_GET, 'id', arg_int())
];
$id = $args['id'];

$membre = DB\query_compte_membre($id);
$pro = DB\query_compte_professionnel($id);
if ($membre !== false) {
    $mdp_hash =$membre['mdp_hash'];
} else if ($pro !== false) {
    $mdp_hash = $pro['mdp_hash'];
} else {
    html_error("le compte d'ID {$args['id']} n'existe pas");
}


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
    $new_nom = getarg($_POST, 'new_nom', null, false);
    if ($new_nom) {
        DB\query_update_Nom($id, $new_nom);
    }

    // modif Prenom ------------------------------------------------------------------------------------------------------------------
    $new_prenom = getarg($_POST, 'new_prenom', null, required: false);
    if ($new_prenom) {
        DB\query_update_prenom($id, $new_prenom);
    }

    // modif Email ------------------------------------------------------------------------------------------------------------------
    $new_email = getarg($_POST, 'new_email', null, false);
    if ($new_email) {
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error_email = "Email incorrect";
          }
          else{
            DB\query_update_email($id, $new_email);

          }
    }
    

    // modif telephone ------------------------------------------------------------------------------------------------------------------
    $new_telephone = getarg($_POST, 'new_telephone', null, false);

    
    if ($new_telephone) {
        if(!preg_match("#^[0-9]{10}$#", $new_telephone)){
            $error_tel = "Numéro incorrect, doit être composé de 10 chiffres";
          
          }
          else {
            DB\query_update_telephone($id, $new_telephone);

          }
    }

    // modif mot de passe ------------------------------------------------------------------------------------------------------------------
    
    $old_mdp = getarg($_POST, 'old_mdp', null, false);
    if($old_mdp){
        $new_mdp = getarg($_POST, 'new_mdp', null, false);
        $confirmation_mdp = getarg($_POST, 'confirmation_mdp', filter: null, required: false);
        // print_r ('test');
        // print_r ($new_mdp);
        // print_r (password_verify($old_mdp, $mdp_hash));
        // print_r ('fin test');
        if (password_verify($old_mdp, $mdp_hash)) {
            # code...
        
            if ($new_mdp) {
                if ($confirmation_mdp === $new_mdp) {
                    DB\query_uptate_mdp($id, password_hash($new_mdp, algo: PASSWORD_DEFAULT));
                } else {
                    $error_mdp = 'Mot de passe de confirmation different.';
                }
            } else {
                $error_mdp = 'Nouveau mot de passe manquant.'; }

        } else {
            $error_mdp = 'Mot de passe incorrect.'; }
    
    }
}

$membre = DB\query_compte_membre($args['id']);
$pro = DB\query_compte_professionnel($args['id']);



if ($membre !== false) {
    // echo '<pre>';
    // print_r($membre);
    // echo '</pre>';
    $pseudo = $membre['pseudo'];
    $email = $membre['email'];
    $mdp_hash =$membre['mdp_hash'];
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
    $mdp_hash = $pro['mdp_hash'];
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
    <form action="modif_compte.php?id=<?= $id ?>" method="POST">


        <a href="<?= location_detail_compte($id) ?>">retour</a>
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
                <?= $siren
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
            <?php if ($error_email !== null) { ?>
                <p class="error"><?= htmlspecialchars($error_email) ?></p>
            <?php } ?>
            
        </div>
        <div>
            <div id="telephone">
                <label>Numero telephone : </label>
                <?= $telephone ?>
            </div>
            <input id="new_telephone" name="new_telephone" type="tel" placeholder="votre nouveau numero telephone">
            <?php if ($error_tel !== null) { ?>
                <p class="error"><?= htmlspecialchars($error_tel) ?></p>
            <?php } ?>
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
                <input id="new_mdp" name="new_mdp" type="password" placeholder="**********">
            </div>
            <div class="champ">
                <label for="mdp">confirmation mot de passe *</label>
                <input id="confirmation_mdp" name="confirmation_mdp" type="password" placeholder="**********">
            </div>
            <?php if ($error_mdp !== null) { ?>
                <p class="error"><?= htmlspecialchars($error_mdp) ?></p>
            <?php } ?>
            <button type="submit">valider</button>
            
        </div>

       


    </form>
</section>

</main>
<?php $page->put_footer() ?>
</body>
</html>
