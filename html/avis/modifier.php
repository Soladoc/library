<?php
session_start();
require_once '../component/db.php'; 
require_once '../component/head.php';

if (!isset($_SESSION['id_membre'])) {
    header("Location: ../connexion.php");
    exit;
}

$id_avis = $_GET['avis_id'];
$id_offre = $_GET['offre'];

// Vérifier que l'utilisateur est l'auteur de l'avis
$requete = "SELECT * FROM pact.avis WHERE id = ? ";
$stmt = db_connect()->prepare($requete);
$stmt->execute([$id_avis]);
$avis = $stmt->fetch();


// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentaire = htmlspecialchars(trim($_POST['commentaire']));
    $note = intval($_POST['rating']);
    $contexte = htmlspecialchars(trim($_POST['contexte']));
    $date_experience = $_POST['date'];

    if (empty($commentaire) || empty($note) || empty($contexte) || empty($date_experience)) {
        $error_message = "Tous les champs sont obligatoires.";
    } else {
        $requete = "UPDATE pact.avis SET commentaire = ?, note = ?, contexte = ?, date_experience = ? WHERE id = ?";
        $stmt = db_connect()->prepare($requete);
        $stmt->execute([$commentaire, $note, $contexte, $date_experience, $id_avis]);

        $success_message = "Avis modifié avec succès !";
        header("Location: ../detail_offre.php?id=$id_offre");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php put_head("Modifier un avis"); ?>

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
                <option value="affaires" <?= $avis['contexte'] == 'affaires' ? 'selected' : '' ?>>Affaires</option>
                <option value="couple" <?= $avis['contexte'] == 'couple' ? 'selected' : '' ?>>Couple</option>
                <option value="solo" <?= $avis['contexte'] == 'solo' ? 'selected' : '' ?>>Solo</option>
                <option value="famille" <?= $avis['contexte'] == 'famille' ? 'selected' : '' ?>>Famille</option>
                <option value="amis" <?= $avis['contexte'] == 'amis' ? 'selected' : '' ?>>Amis</option>
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
