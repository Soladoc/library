<?php
require_once 'util.php';
require_once 'auth.php';
require_once 'queries.php';
require_once 'redirect.php';
require_once 'component/Page.php';
require_once 'component/InputAdresse.php';
require_once 'model/Compte.php';

$page = new Page('Modification compte (todo)');
$error_mdp = null;
$error_tel = null;
$error_email = null;
$error_siren = null;

$compte = notfalse(Compte::from_db(Auth\id_compte_connecte()));

$input_adresse = new InputAdresse('adresse', 'adresse');

// Afficher le détail du compte du membre

if ($_POST) {
    // modif pseudo
    $new_pseudo = getarg($_POST, 'new_pseudo', null, required: false);
    if ($new_pseudo) {
        DB\query_update_pseudo($compte->id, $new_pseudo);
    }

    // modif denomination
    $new_denomination = getarg($_POST, 'new_denomination', null, false);
    if ($new_denomination) {
        DB\query_update_denomination($compte->id, $new_denomination);
    }

    // modif siren
    $new_siren = getarg($_POST, 'new_siren', null, false);
    if ($new_siren) {
        if (!preg_match('#^[0-9]{9}$#', $new_siren)) {
            $error_siren = 'siren incorrect, doit être composé de 9 chiffres';
        } else {
            DB\query_update_siren($compte->id, $new_siren);
        }
    }

    // modif Nom
    $new_nom = getarg($_POST, 'new_nom', null, false);
    if ($new_nom) {
        DB\query_update_Nom($compte->id, $new_nom);
    }

    // modif Prenom
    $new_prenom = getarg($_POST, 'new_prenom', null, required: false);
    if ($new_prenom) {
        DB\query_update_prenom($compte->id, $new_prenom);
    }

    // modif Email
    $new_email = getarg($_POST, 'new_email', null, false);
    if ($new_email) {
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error_email = 'Email incorrect';
        } else {
            DB\query_update_email($compte->id, $new_email);
        }
    }

    // modif telephone
    $new_telephone = getarg($_POST, 'new_telephone', null, false);

    if ($new_telephone) {
        if (!preg_match('#^[0-9]{10}$#', $new_telephone)) {
            $error_tel = 'Numéro incorrect, doit être composé de 10 chiffres';
        } else {
            DB\query_update_telephone($compte->id, $new_telephone);
        }
    }

    // modif mot de passe

    $old_mdp = getarg($_POST, 'old_mdp', null, false);
    if ($old_mdp) {
        $new_mdp = getarg($_POST, 'new_mdp', null, false);
        $confirmation_mdp = getarg($_POST, 'confirmation_mdp', filter: null, required: false);
        if (password_verify($old_mdp, $compte->mdp_hash)) {
            if ($new_mdp) {
                if ($confirmation_mdp === $new_mdp) {
                    DB\query_update_mdp($compte->id, password_hash($new_mdp, algo: PASSWORD_DEFAULT));
                } else {
                    $error_mdp = 'Mot de passe de confirmation different.';
                }
            } else {
                $error_mdp = 'Nouveau mot de passe manquant.';
            }
        } else {
            $error_mdp = 'Mot de passe incorrect.';
        }
    }
}

$page->put(function () use (
    $compte,
    $input_adresse,
    $error_email,
    $error_mdp,
    $error_siren,
    $error_tel
) {
    ?>
    <h1>Modifier les informations de votre compte</h1>
    <section id="info_compte">
        <form action="modif_compte.php" method="post">
            <?php if ($compte instanceof Membre) { ?>
                <div>
                    <div id="pseudo">
                        <label>Pseudo : </label>
                    </div>
                    <input id="new_pseudo" name="new_pseudo" type="text" value="<?= h14s($compte->pseudo) ?>" placeholder="votre nouveau pseudo">
                </div>
                <br>
            <?php } else if ($compte instanceof Professionnel) { ?>
                    <div>
                        <div id="denomination">
                            <label>Dénomination : </label>
                        </div>
                        <input id="new_denomination" name="new_denomination" type="text" value="<?= h14s($compte->denomination) ?>" placeholder="votre nouvelle dénomination">
                    </div>
                    <br>
                <?php if ($compte instanceof ProfessionnelPrive) { ?>
                        <div>
                            <div id="siren">
                                <label>SIREN : </label>
                            </div>
                        <?php if ($error_siren !== null) { ?>
                                <p class="error"><?= h14s($error_siren) ?></p>
                        <?php } ?>
                            <input type="text" id="new_siren" name="new_siren" value="<?= h14s($compte->siren) ?>" placeholder="231 654 988" oninput="formatInput(this)" maxlength="12">
                        </div>
                        <br>
                <?php } ?>
            <?php } ?>

            <div>
                <div id="nom">
                    <label>Nom : </label>
                </div>
                <input id="new_nom" name="new_nom" type="text" value="<?= h14s($compte->nom) ?>" placeholder="votre nouveau nom">
            </div>
            <br>
            <div>
                <div id="prenom">
                    <label>Prénom : </label>
                </div>
                <input id="new_prenom" name="new_prenom" type="text" value="<?= h14s($compte->prenom) ?>" placeholder="votre nouveau prénom">
            </div>
            <br>
            <div>
                <div id="email">
                    <label>Email : </label>
                </div>
                <?php if ($error_email !== null) { ?>
                    <p class="error"><?= h14s($error_email) ?></p>
                <?php } ?>
                <input id="new_email" name="new_email" type="email" value="<?= h14s($compte->email) ?>" placeholder="votre nouvel email">

            </div>
            <br>
            <div>
                <div id="telephone">
                    <label>Numéro de téléphone : </label>
                </div>
                <?php if ($error_tel !== null) { ?>
                    <p class="error"><?= h14s($error_tel) ?></p>
                <?php } ?>
                <input id="new_telephone" name="new_telephone" type="tel" value="<?= h14s($compte->telephone) ?>" placeholder="votre nouveau numéro de téléphone">

            </div>
            <br>
            <div>
                <label>Adresse : </label>
                <?= $compte->adresse->format() ?>
            </div>
            <?php $input_adresse->put($compte->adresse) ?>
            <br>
            <div id='changer_mdp'>
                <label>Modifier son mot de passe</label>
                <div class="champ">
                    <label for="mdp">Mot de passe actuel *</label>
                    <input id="mdp" name="old_mdp" type="password" placeholder="**********">
                </div>
                <div class="champ">
                    <label for="new_mdp">Nouveau mot de passe *</label>
                    <input id="new_mdp" name="new_mdp" type="password" placeholder="**********">
                </div>
                <div class="champ">
                    <label for="confirmation_mdp">Confirmation du mot de passe *</label>
                    <input id="confirmation_mdp" name="confirmation_mdp" type="password" placeholder="**********">
                </div>
                <?php if ($error_mdp !== null) { ?>
                    <p class="error"><?= h14s($error_mdp) ?></p>
                <?php } ?>

            </div>
            <button type="submit">Valider</button>
            <a href="<?= location_detail_compte() ?>">Retour</a>
            <?php Compte::from_db($compte->id)->delete() ?>
            <a href="/connexion/logout.php">Supprimer le compte</a>
        </form>
    </section>
    <?php
});