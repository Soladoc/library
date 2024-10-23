<?php
session_start();

// Configuration de la base de données
require_once 'db.php';

try {
    $pdo = db_connect();  // Essaie de te connecter à la base de données
} catch (PDOException $e) {
    // Gérer l'erreur de connexion
    die('Échec de la connexion à la base de données : ' . $e->getMessage());
}

// Récupérer les données du formulaire
$username = trim($_POST['login']);
$password = trim($_POST['mdp']);

// Vérifier que les champs ne sont pas vides
if (empty($username) || empty($password)) {
    header('Location: ../autres_pages/connexion.php?error=Veuillez remplir tous les champs.');
    exit();
}

// Préparer et exécuter la requête pour éviter les injections SQL
$stmt = $pdo->prepare('SELECT email, mdp_hash, existe FROM pact.membres WHERE email = :email');
$stmt->bindValue(':email', $username, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo $user['existe'],$user['email'],$user['mdp_hash'];

if (!empty($user) && $user['existe'] == 1) {
    $hashed_password = $user['mdp_hash'];
    if (password_verify($password, $hashed_password)) {
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        header("Location: ../autres_pages/accueil.php");
        exit();
        
    } else {
        header("Location: ../autres_pages/connexion.php?error=Nom d'utilisateur ou mot de passe incorrect.");
        exit();
    }
} else {
    // Vérifier si l'utilisateur existe dans la table professionnel
    $stmt = $pdo->prepare('SELECT email, mdp_hash, existe FROM pact.tous_comptes_pro WHERE email = :email');
    $stmt->bindValue(':email', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user) && $user['existe'] == 1) {
        $hashed_password = $user['mdp_hash'];
        if (password_verify($password, $hashed_password)) {
            session_regenerate_id(true);
            $_SESSION['username'] = $username;

            header("Location: ../autres_pages/accPro.php");
            exit();
        } else {
            header("Location: ../autres_pages/connexion.php?error=Nom d'utilisateur ou mot de passe incorrect.");
            exit();
        }
    } else {
        header("Location: ../autres_pages/connexion.php?error=Nom d'utilisateur ou mot de passe incorrect.");
        exit();
    }
}

$stmt = null;
$pdo = null;
?>
