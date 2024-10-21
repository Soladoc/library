<?php
session_start();

// Configuration de la base de données
require '../../db.php';

try {
    $pdo = db_connect(); // Essaie de te connecter à la base de données
} catch (PDOException $e) {
    // Gérer l'erreur de connexion
    die("Échec de la connexion à la base de données : " . $e->getMessage());
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
$stmt = $pdo->prepare("SELECT email, mdp_hash FROM pact._compte WHERE email = :email");
$stmt->bindValue(':email', $username, PDO::PARAM_STR);

$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if ($user) {
    
    $hashed_password = $user['mdp_hash'];
    echo "Hash récupéré : " . $hashed_password;
    echo "2";
    if (password_verify($password, $hashed_password)) {
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        echo "connecté";
        exit();
    } else {
        // Mot de passe incorrect
        header("Location: ../autres_pages/connexion.php?error=Nom d'utilisateur ou mot de passe incorrect.");
        exit();
    }
} else {
    echo "echec";
    exit();
}

$stmt = null;
$pdo = null;
?>
