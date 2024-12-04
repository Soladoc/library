<?php
require_once '../util.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $secret_key = 'votre_cle_secrete';

    // Décoder le token
    [$payload_base64, $signature] = explode('.', $token);
    $payload = json_decode(base64_decode($payload_base64), true);

    // Vérification du token
    if (hash_hmac('sha256', $payload_base64, $secret_key) === $signature && $payload['expire_at'] > time()) {
        // Le token est valide
        $user_id = $payload['user_id'];
        $user_type = $payload['user_type'];

        // Afficher le formulaire pour changer le mot de passe
        echo '<form action="update_password.php" method="post">
                <input type="hidden" name="user_id" value="' . htmlspecialchars($user_id) . '">
                <input type="hidden" name="user_type" value="' . htmlspecialchars($user_type) . '">
                <label for="password">Nouveau mot de passe :</label>
                <input type="password" name="password" required>
                <button type="submit">Réinitialiser</button>
              </form>';
    } else {
        echo "Le lien est invalide ou expiré.";
    }
} else {
    echo "Token manquant.";
}
?>
