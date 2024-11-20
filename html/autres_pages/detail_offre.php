<?php
require_once 'component/offre.php';
require_once 'component/head.php';
require_once 'db.php'; // Assurez-vous que ce fichier connecte correctement à la base de données.

session_start();

$pdo = db_connect();

if ($_POST) {
    if (isset($_POST['commentaire'], $_POST['date'], $_POST['rating'], $_POST['consent'])) {
        $query = "
            INSERT INTO pact._avis (
                commentaire, 
                note, 
                date_experience, 
                id_membre_auteur, 
                id_offre
            ) VALUES (?, ?, ?, ?, ?)
            ON CONFLICT (id_membre_auteur, id_offre) DO UPDATE SET
                commentaire = EXCLUDED.commentaire,
                note = EXCLUDED.note,
                date_experience = EXCLUDED.date_experience,
                moment_publication = now();
        ";

        $stmt = $pdo->prepare($query);

        $id_membre = $_SESSION['id_membre'] ?? null; // Membre connecté ou `null` si anonyme
        $id_offre = $_GET['id']; // L'offre en cours

        $stmt->execute([
            $_POST['commentaire'],  // `commentaire`
            $_POST['rating'],       // `note`
            $_POST['date'],         // `date_experience`
            $id_membre,             // `id_membre_auteur`
            $id_offre,              // `id_offre`
        ]);

        $success_message = "Avis ajouté ou mis à jour avec succès !";
    } else {
        $error_message = "Veuillez remplir tous les champs du formulaire.";
    }
}

$args = [
    'id' => getarg($_GET, 'id', arg_filter(FILTER_VALIDATE_INT))
];

$offre = query_offre($args['id']);
if ($offre === false) {
    put_head("Erreur ID");
    require 'component/header.php';
    echo "<p>L'offre que vous cherchez n'existe pas</p>";
    require 'component/footer.php';
    exit;
}
assert($offre['id'] === $args['id']);

$titre = $offre['titre'];
$description = $offre['description_detaillee'];
$site_web = $offre['url_site_web'];
$image_principale = query_image($offre['id_image_principale']);
$adresse = notfalse(query_adresse($offre['id_adresse']));
$gallerie = query_gallerie($args['id']);
$avis = query_avis($args['id']); // Récupère tous les avis liés à cette offre
?>

<!DOCTYPE html>
<html lang="fr">
<?php 
put_head("Offre : {$args['id']}");
?>

<body>
    <?php require 'component/header.php'; ?>
    
    <main>
        <section class="offer-details">
            <div class="offer-main-photo">
                <?php put_image($image_principale); ?>
                <div class="offer-photo-gallery">
                    <?php foreach ($gallerie as $image) {
                        put_image(query_image($image));
                    } ?>
                </div>
            </div>

            <div class="offer-info">
                <h2><?= htmlspecialchars($titre) ?></h2>
                <p class="description"><?= htmlspecialchars($description) ?></p>
                <div class="offer-status">
                    <p><strong>Site web&nbsp;:</strong> <a href="<?= htmlspecialchars($site_web) ?>"><?= htmlspecialchars($site_web) ?></a></p>
                    <p><strong>Adresse&nbsp;:</strong> <?= format_adresse($adresse) ?></p>
                </div>
            </div>
        </section>

        <!-- User Reviews -->
        <section class="offer-reviews">
            <h3>Avis des utilisateurs</h3>

            <!-- Formulaire d'avis -->
            <div class="review-form">
                <?php if (isset($success_message)) echo "<p class='success'>{$success_message}</p>"; ?>
                <?php if (isset($error_message)) echo "<p class='error'>{$error_message}</p>"; ?>

                <form method="post">
                    <textarea name="commentaire" placeholder="Votre avis..." required></textarea>
                    <label for="rating">Note&nbsp;:</label>
                    <select id="rating" name="rating" required>
                        <option value="5">5 étoiles</option>
                        <option value="4">4 étoiles</option>
                        <option value="3">3 étoiles</option>
                        <option value="2">2 étoiles</option>
                        <option value="1">1 étoile</option>
                    </select>
                    <label for="date">Date de votre visite</label>
                    <input type="date" id="date" name="date" required>
                    </br>
                    <label>
                        <input type="checkbox" name="consent" required>
                        Je certifie que l’avis reflète ma propre expérience et mon opinion sur cette offre.
                    </label>
                    <button type="submit" class="btn-publish">Publier</button>
                </form>
            </div>

            <!-- Résumé des avis -->
            <div class="review-summary">
                <h4>Résumé des notes</h4>
                <p>Moyenne&nbsp;: <?= htmlspecialchars($offre['note_moyenne'] ?? "N/A") ?>/5 ★</p>
                <div class="rating-distribution">
                    <?php
                    $ratings = array_count_values(array_column($avis, 'note'));
                    for ($i = 5; $i >= 1; $i--) {
                        echo "<p>{$i} étoile(s)&nbsp;: " . ($ratings[$i] ?? 0) . " avis</p>";
                    }
                    ?>
                </div>
            </div>

            <!-- Liste des avis -->
            <div class="review-list">
                <h4>Avis de la communauté</h4>
                <?php foreach ($avis as $avis_temp) { ?>
                    <div class="review">
                        <p><strong><?= htmlspecialchars($avis_temp['pseudo'] ?? "Anonyme") ?></strong> - <?= $avis_temp['note'] ?>/5</p>
                        <p><?= htmlspecialchars($avis_temp['commentaire']) ?></p>
                        <p><small>Publié le <?= htmlspecialchars($avis_temp['moment_publication']) ?></small></p>
                    </div>
                <?php } ?>
            </div>
        </section>
    </main>

    <?php require 'component/footer.php'; ?>
</body>
</html>


