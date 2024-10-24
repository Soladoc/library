<?php
session_start(); // Démarrer la session

// Supprimer toutes les variables de session
$_SESSION = array(); // Réinitialiser toutes les variables de session

// Détruire la session
session_destroy(); // Met fin à la session active

// Rediriger vers la page de connexion ou la page d'accueil
header("Location: ../autres_pages/accueil.php");
exit();
?>