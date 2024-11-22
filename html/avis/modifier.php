<?php
session_start();
require_once 'auth.php';
require_once 'component/head.php';
require_once 'const.php';
require_once 'db.php';

exiger_connecte_membre();

$id_avis = intval($_GET['avis_id']);
$id_offre = intval($_GET['offre']);

$stmt = db_connect()->prepare('SELECT * FROM pact._avis WHERE id = ?');
$stmt->execute([$id_avis]);
$avis = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire si la méthode POST est utilisée
if (isset($_POST['date'])) {
    $commentaire = htmlspecialchars(trim($_POST['commentaire']));
    $note = intval($_POST['rating']);
    $contexte = htmlspecialchars(trim($_POST['contexte']));
    $date_experience = $_POST['date'];

    // Validation des champs du formulaire
    if (empty($commentaire) || empty($note) || empty($contexte) || empty($date_experience)) {
        $error_message = 'Tous les champs sont obligatoires.';
    } else {
        // Mise à jour de l'avis dans la base de données
        $stmt = db_connect()->prepare('UPDATE pact._avis SET commentaire = ?, note = ?, contexte = ?, date_experience = ? WHERE id = ?');
        $stmt->execute([$commentaire, $note, $contexte, $date_experience, $id_avis]);

        $success_message = 'Avis modifié avec succès !';
        $id = $avis['id_offre'];
        header("Location: ../autres_pages/detail_offre.php?id=$id");
        exit;
    }

} else {
?>
<!DOCTYPE html>
<html lang="en">

<?php put_head('Modifier un avis'); ?>

<body>
    <?php require 'component/header.php'; ?>
    <main>
        <h2>Modifier votre avis</h2>

        <div class="message">
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
            <?php elseif (isset($success_message)): ?>
                <p class="success-message"><?= htmlspecialchars($success_message) ?></p>
            <?php endif; ?>
        </div>

        <form method="post" action="modifier.php?id=<?= $id_offre ?>&avis_id=<?= $id_avis ?>">
            <textarea name="commentaire" placeholder="Votre avis..." required><?= htmlspecialchars($avis['commentaire']) ?></textarea>

            <label for="rating">Note&nbsp;:</label>
            <select name="rating" id="rating" required>
                <option value="5" <?= $avis['note'] == 5 ? 'selected' : '' ?>>5 étoiles</option>
                <option value="4" <?= $avis['note'] == 4 ? 'selected' : '' ?>>4 étoiles</option>
                <option value="3" <?= $avis['note'] == 3 ? 'selected' : '' ?>>3 étoiles</option>
                <option value="2" <?= $avis['note'] == 2 ? 'selected' : '' ?>>2 étoiles</option>
                <option value="1" <?= $avis['note'] == 1 ? 'selected' : '' ?>>1 étoile</option>
            </select>

            <label for="contexte">Contexte&nbsp;:</label>
            <select name="contexte" id="contexte" required>
                <?php foreach (CONTEXTES_VISITE as $ctx) { ?>
                    <option value="<?= $ctx ?>" <?= $avis['contexte'] === $ctx ? 'selected' : '' ?>><?= ucfirst($ctx) ?></option>
                <?php } ?>
            </select>

            <label for="date">Date de votre visite</label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($avis['date_experience']) ?>" required>
            
            <br>
            <button type="submit" class="btn-publish">Modifier</button>
        </form>
    </main>
    <?php require 'component/footer.php'; ?>
</body>
</html>
<?php } ?>