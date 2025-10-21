<?php
require_once 'model/Offre.php';

/**
 * Redirige l'utilisateur vers une URL, mettant fin au script actuel.
 * Cette fonction appelle <i>header</i>. Elle doit donc être appelée <b>avant</b> tout envoi de HTML.
 * @param string $location L'URL où rediriger l'utilisateur.
 * @return never
 */
function redirect_to(string $location): never
{
    header("Location: $location");
    exit;
}

/**
 * Obtient l'URL de la page de connexion.
 * @param ?string $error L'erreur à afficher. `null` pour pas d'erreur.
 * @param ?string $return_url L'URL ou rediriger lorsque la connexion réussit. `null` indique de rediriger l'utilisateur vers la page d'accueil (pro ou membre).
 * @return string L'URL de la page de connection.
 */
function location_connexion(?string $error = null, ?string $return_url = null, ?string $pseudo = null): string
{
    return '/autres_pages/connexion.php?' . http_build_query(['error' => $error, 'return_url' => $return_url, 'pseudo' => $pseudo]);
}

/**
 * Obtient l'URL de la page de détail d'offre.
 * @param int $id_offre L'ID de l'offre détaillée.
 * @return string L'URL de la page de détail d'offre.
 */
function location_detail_offre(int $id_offre): string
{
    return '/autres_pages/detail_offre.php?' . http_build_query(['id' => $id_offre]);
}

/**
 * Obtient l'URL de la page de détail d'offre professionnel.
 * @param int $id_offre L'ID de l'offre détaillée.
 * @return string L'URL de la page de détail d'offre professionnel.
 */
function location_detail_offre_pro(int $id_offre): string
{
    return '/autres_pages/detail_offre_pro.php?' . http_build_query(['id' => $id_offre]);
}

function location_creation_offre(): string
{
    return '/autres_pages/choix_categorie_creation_offre.php';
}

function location_detail_compte(): string
{
    return '/autres_pages/detail_compte.php';
}

function location_modifier_offre(Offre $offre): string
{
    return '/autres_pages/modif_offre.php?' . http_build_query(['categorie' => $offre->categorie, 'id' => $offre->id]);
}

function location_modifier_compte(int $id, ?string $error = null): string
{
    return '/autres_pages/modif_compte.php?' . http_build_query(['id' => $id, 'return_url' => $_SERVER['REQUEST_URI'], 'error' => $error]);
}

function location_supprimer_compte(int $id_compte): string
{
    return '/auto/supprimer_compte.php?' . http_build_query(['id_compte' => $id_compte]);
}

function location_login(): string
{
    return '/auto/login.php';
}

function location_logout(): string
{
    return '/auto/logout.php';
}

function location_mentions_legales()
{
    return '/autres_pages/legal/mentions-legales.php';
}

function location_cgu()
{
    return '/autres_pages/legal/cgu.php';
}

function location_cgv()
{
    return '/autres_pages/legal/cgv.php';
}
