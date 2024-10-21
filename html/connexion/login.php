<?php
session_start();

// Configuration de la base de données
require '../db.php';
$pdo = db_connect();

print_r("ok");
// Vérifier la connexion
if ($pdo->connect_error) {
    die("Échec de la connexion : " . $pdo->connect_error);
}

// Récupérer les données du formulaire
$username = trim($_POST['login']);
$password = trim($_POST['mdp']);

// Vérifier que les champs ne sont pas vides
if (empty($username) || empty($password)) {
    header("Location: ../autres_pages/connexion.php?error=Veuillez remplir tous les champs.");
    exit();
}

// Préparer et exécuter la requête pour éviter les injections SQL
$stmt = $pdo->prepare("SELECT email, mdp_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

// Vérifier si l'utilisateur existe
if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    // Vérifier le mot de passe
    if (password_verify($password, $hashed_password)) {
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        header("Location: ../autres_pages/accueil.php");
        exit();
    } else {
        // Mot de passe incorrect
        header("Location: ../autres_pages/connexion.php?error=Nom d'utilisateur ou mot de passe incorrect.");
        exit();
    }
} else {
    header("Location: ../autres_pages/connexion.php?error=Nom d'utilisateur ou mot de passe incorrect.");
    exit();
}

$stmt->close();
$pdo->close();
?>
