<?php
session_start();
require_once 'component/head.php';
?>
<!DOCTYPE html>
<html lang="en">

<?php put_head('Modifier un avis'); ?>

<body>
    <?php require '../component/header.php'; ?>
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
    <?php require '../component/footer.php'; ?>
</body>
</html>