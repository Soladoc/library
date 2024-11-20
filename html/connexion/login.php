<?php
session_start();

require_once 'queries.php';
require_once 'auth.php';
require_once 'util.php';

// Récupérer les données du formulaire
$args = [
    'login' => getarg($_POST, 'login'),
    'mdp' => getarg($_POST, 'mdp'),
];

$pdo = db_connect();

// Connection membre
$user = query_membre($args['login']);

if (!empty($user)) {
    if (!password_verify($args['mdp'], $user['mdp_hash'])) {
        fail();
    }
    session_regenerate_id(true);
    connecter_membre($user['id']);
    header('Location: /autres_pages/accueil.php');
    exit;
}

// Connection professionnel
$user = query_professionnel($args['login']);

if (!empty($user)) {
    if (!password_verify($args['mdp'], $user['mdp_hash'])) {
        fail();
    }
    session_regenerate_id(true);
    connecter_pro($user['id']);
    header('Location: /autres_pages/accPro.php');
    exit;
}

fail();

function fail(): never
{
    header('Location: /autres_pages/connexion.php?error=' . urlencode("Nom d'utilisateur ou mot de passe incorrect."));
    exit;
}