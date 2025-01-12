$page->put(function () use (
    $compte,
    $input_adresse,
    $error_email,
    $error_mdp,
    $error_siren,
    $error_tel
) {
    ?>
    <div class="body-center">
        <form action="modif_compte.php" method="post" class="form-container">
            <h1 class="form-title">Modifier les informations de votre compte</h1>
            
            <?php if ($compte instanceof Membre) { ?>
                <div>
                    <label class="label">Pseudo :</label>
                    <input class="input-field" name="new_pseudo" type="text" 
                        value="<?= h14s($compte->pseudo) ?>" placeholder="Votre nouveau pseudo">
                </div>
            <?php } else if ($compte instanceof Professionnel) { ?>
                <div>
                    <label class="label">Dénomination :</label>
                    <input class="input-field" name="new_denomination" type="text" 
                        value="<?= h14s($compte->denomination) ?>" placeholder="Votre nouvelle dénomination">
                </div>
                <?php if ($compte instanceof ProfessionnelPrive) { ?>
                    <div>
                        <label class="label">SIREN :</label>
                        <?php if ($error_siren !== null) { ?>
                            <p class="error-text"><?= h14s($error_siren) ?></p>
                        <?php } ?>
                        <input class="input-field" type="text" name="new_siren" 
                            value="<?= h14s($compte->siren) ?>" placeholder="231654988" maxlength="12">
                    </div>
                <?php } ?>
            <?php } ?>

            <div>
                <label class="label">Nom :</label>
                <input class="input-field" name="new_nom" type="text" 
                    value="<?= h14s($compte->nom) ?>" placeholder="Votre nouveau nom">
            </div>

            <div>
                <label class="label">Prénom :</label>
                <input class="input-field" name="new_prenom" type="text" 
                    value="<?= h14s($compte->prenom) ?>" placeholder="Votre nouveau prénom">
            </div>

            <div>
                <label class="label">Email :</label>
                <?php if ($error_email !== null) { ?>
                    <p class="error-text"><?= h14s($error_email) ?></p>
                <?php } ?>
                <input class="input-field" name="new_email" type="email" 
                    value="<?= h14s($compte->email) ?>" placeholder="Votre nouvel email">
            </div>

            <div>
                <label class="label">Téléphone :</label>
                <?php if ($error_tel !== null) { ?>
                    <p class="error-text"><?= h14s($error_tel) ?></p>
                <?php } ?>
                <input class="input-field" name="new_telephone" type="tel" 
                    value="<?= h14s($compte->telephone) ?>" placeholder="Votre nouveau numéro">
            </div>

            <div>
                <label class="label">Adresse :</label>
                <?= $compte->adresse->format() ?>
                <?= $input_adresse->put($compte->adresse) ?>
            </div>

            <div>
                <label class="label">Modifier le mot de passe</label>
                <div>
                    <label class="label">Mot de passe actuel</label>
                    <input class="input-field" name="old_mdp" type="password" placeholder="**********">
                </div>
                <div>
                    <label class="label">Nouveau mot de passe</label>
                    <input class="input-field" name="new_mdp" type="password" placeholder="**********">
                </div>
                <div>
                    <label class="label">Confirmation du mot de passe</label>
                    <input class="input-field" name="confirmation_mdp" type="password" placeholder="**********">
                </div>
                <?php if ($error_mdp !== null) { ?>
                    <p class="error-text"><?= h14s($error_mdp) ?></p>
                <?php } ?>
            </div>

            <button type="submit" class="button">Valider</button>
            <a href="<?= location_detail_compte() ?>" class="button">Retour</a>
        </form>
    </div>
    <?php
});
