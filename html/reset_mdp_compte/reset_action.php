<?php
require_once 'util.php';
require_once 'queries.php';
require_once 'model/Compte.php';

if ($_POST) {
    $email = trim($_POST['login']);
    $user = Compte::from_db_by_email($email);

    if ($user) {
        $user_id = $user->id;
        $user_type = isset($user->pseudo) ? 'membre' : 'professionnel';

        // Création du token signé
        $payload = [
            'user_id' => $user_id,
            'user_type' => $user_type,
            'expire_at' => time() + 3600 // Expiration dans 1 heure
        ];
        $secret_key = 'votre_cle_secrete'; // Remplacez par une clé sécurisée
        $token = base64_encode(json_encode($payload)) . '.' . hash_hmac('sha256', json_encode($payload), $secret_key);

        // Créer le lien de réinitialisation
        $reset_link = "https://413.ventsdouest.dev/changer_mpd/reset_form.php?token=$token";

        // Envoyer un email avec le lien de réinitialisation
        $subject = "Réinitialisation de votre mot de passe";
        $message = "Bonjour,\n\nCliquez sur ce lien pour réinitialiser votre mot de passe : $reset_link\n\nCe lien expirera dans une heure.";
        $headers = "From: no-reply@ventsdouest.dev";

        mail($user->email, $subject, $message, $headers);

        header('Location: ../autres_pages/reset_mdp.php?success=1');
        exit;
    } else {
        header('Location: ../autres_pages/reset_mdp.php?error=Utilisateur%20non%20trouvé');
        exit;
    }
}
?>
