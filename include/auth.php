<?php

/**
 * auth.php
 * Fonctions relatives à l'authentification
 */

namespace Auth;

require_once 'util.php';
require_once 'redirect.php';

notfalse(session_start());

function location_home(): string
{
    return '/autres_pages/connexion.php';
}

/**
 * Déconnecte la session actuelle et redirige vers l'accueil.
 * @return never
 */
function se_deconnecter(): never
{
    assert(est_connecte());
    notfalse(session_unset());
    $_SESSION = [];
    notfalse(session_destroy());
    notfalse(session_regenerate_id(true));
    redirect_to(location_home());
}

/**
 * Sommes-nous connectés?
 * @return bool `true` si nous (la session courante) sommes connectés, `false` sinon.
 */
function est_connecte(): bool
{
    return isset($_SESSION['id']);
}

/**
 * Marque la session comme connectée à une compte.
 * @param int $id L'ID du compte qui sera désormais connecté sur la session actuelLe. Correspond à numero_compte dans la bdd.
 * @return void
 */
function se_connecter(int $id): void
{
    $_SESSION['id'] = $id;
}

/**
 * Retourne l'ID du compte actuellement connecté, redirigeant vers la page de connexion sinon.
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelée <b>avant</b> tout envoi de HTML.
 * @return int L'ID du compte actuellement connecté.
 */
function exiger_connecte(): int
{
    if (($id = id_connecte()) !== null) {
        return $id;
    }
    redirect_to('/autres_pages/connexion.php');
    exit;
}

/**
 * Retourne l'ID du compte actuellement connecté.
 * @return ?int L'ID du compte actuellement connecté, ou `null` si la session actuelle n'est pas connectée en tant que professionnel.
 */
function id_connecte(): ?int
{
    return $_SESSION['id'] ?? null;
}
