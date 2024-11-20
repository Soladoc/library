<?php
session_start();
require_once 'component/offre.php';
require_once 'component/head.php';

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['id_membre'])) {
    header("Location: login.php");
    exit;
}

// Récupération de l'ID de l'avis à modifier
$avis_id = getarg($_GET, 'avis_id', arg_filter(FILTER_VALIDATE_INT));
$args = [
    'id' => getarg($_GET, 'id', arg_filter(FILTER_VALIDATE_INT)),
];

// Récupérer les informations de l'avis
$avis = query_avis_by_id($avis_id);
if ($avis === false || $avis['id_membre_auteur'] !== $_SESSION['id_membre']) {
    // Si l'avis n'existe pas ou si l'utilisateur essaie de modifier un avis qui ne lui appartient pas
    header("Location: detail_offre.php?id=" . $args['id']);
    exit;
}

if ($_POST) {
    $args += [
        'commentaire' => getarg($_POST, 'commentaire'),
        'date_avis' => getarg($_POST, 'date'),
        'note' => getarg($_POST, 'rating', arg_filter(FILTER_VALIDATE_INT)),
        'contexte' => getarg($_POST, 'contexte'),
    ];

    // Mise à jour de l'avis
    $querry = "UPDATE pact.avis SET commentaire = ?, date_experience = ?, note = ?, contexte = ? WHERE id = ? AND id_membre_auteur = ?";
    $stmt = db_connect()->prepare($querry);
    $stmt->execute([
        $args['commentaire'],
        $args['date_avis'],
        $args['note'],
        $args['contexte'],
        $avis_id,
        $_SESSION['id_membre']
    ]);

    $success_message = "Avis modifié avec succès !";
}

// Récupération des informations de l'offre
$offre = query_offre($args['id']);
if ($offre === false) {
    header("Location: index.php"); // Si l'offre n'existe pas
    exit;
}

$titre = $offre['titre'];
$description = $offre['description_detaillee'];
$site_web = $offre['url_site_web'];
$image_pricipale = query_image($offre['id_image_principale']);
$adresse = notfalse(query_adresse($offre['id_adresse']));
?>

<!DOCTYPE html>
<html lang="fr">

<?php put_head("Modifier l'avis : {$args['id']}",
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'],
    ['https://unpkg.com/leaflet@1.7.1/dist/leaflet.js' => 'async']); 
?>

<body>
    <?php require 'component/header.php' ?>

    <main>
        <section class="offer-details">
            <h2>Modifier votre avis</h2>

            <div class="message">
                <?php if (isset($success_message)): ?>
                    <p class="success-message"><?= htmlspecialchars($success_message) ?></p>
                <?php endif; ?>
            </div>

            <form method="post" action="modifier.php?id=<?= $args['id'] ?>&avis_id=<?= $avis_id ?>">
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

                <button type="submit" class="btn-publish">Mettre à jour l'avis</button>
            </form>
        </section>
    </main>

    <?php require 'component/footer.php' ?>

</body>
</html>
