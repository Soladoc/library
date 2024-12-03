<?php
require_once 'component/Page.php';
require_once 'util.php';

$page = new Page('Reinitialiser le mot de passe');

$return_url = getarg($_GET, 'return_url', required: false);
$error = getarg($_GET, 'error', required: false);
?>

<!DOCTYPE html>
<html lang="fr">

<?php $page->put_head() ?>

<body>
<?php $page->put_header() ?>
<main>
    <h1>Réinitialiser le mot de passe</h1>
    <section class="connexion">
        <div class="champ-connexion">
            <br>
            <!-- Formulaire de connexion -->
            <form action="../reset_mdp_compte/reset_action.php" method="POST">
                <div class="champ">
                    <label for="login">Adresse e-mail *</label>
                    <input id="login" name="login" type="text" placeholder="exemple@mail.fr" required>
                </div>
                <br>
                <?php if ($error !== null) { ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php } ?>
                <button type="submit" class="btn-connexion">Envoyer un mail</button>
                <?php if ($return_url !== null) { ?>
                    <input type="hidden" name="return_url" value="<?= htmlspecialchars($return_url) ?>">
                <?php } ?>
            </form>
            <br><br>
            <a href="connexion.php" class="btn-creer">Retour</a>
            <br>
        </div>
    </section>
</main>
<?php $page->put_footer() ?>
</body>

</html>
