<?php
require_once 'auth.php';
require_once 'util.php';
require_once 'queries.php';
require_once 'redirect.php';

// Récupérer les données du formulaire
$args = [
    'login' => getarg($_POST, 'login'),
    'mdp' => getarg($_POST, 'mdp'),
    'return_url' => getarg($_POST, 'return_url', required: false),
];

// Connection membre
$user = DB\query_membre($args['login']);

if (!empty($user)) {
    if (!password_verify($args['mdp'], $user['mdp_hash'])) {
        fail();
    }
    session_regenerate_id(true);
    Auth\se_connecter_membre($user['id']);
    succeed('/autres_pages/accueil.php');
}

// Connection professionnel
$user = DB\query_professionnel($args['login']);

if (!empty($user)) {
    if (!password_verify($args['mdp'], $user['mdp_hash'])) {
        fail();
    }
    session_regenerate_id(true);
    Auth\se_connecter_pro($user['id']);
    succeed('/autres_pages/accPro.php');
}

fail();

function fail(): never
{
    redirect_to(location_connexion(error: "Nom d'utilisateur ou mot de passe incorrect."));
    exit;
}

function succeed(string $default_location): never
{
    global $args;
    redirect_to($args['return_url'] ?? $default_location);
    exit;
}
