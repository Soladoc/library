<?php

/**
 * Redirige l'utilisateur vers une URL, mettant fin au script actuel.
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
function location_connexion(?string $error = null, ?string $return_url = null): string
{
    
    return '/autres_pages/connexion.php?' . http_build_query(['error' => $error, 'return_url' => $return_url]);
}

/**
 * Obtient l'URL de la page de détail d'offre.
 * @param int $id_offre L'ID de l'offre détaillée.
 * @return string L'URL de la page de détail d'offre.
 */
function location_detail_offre(int $id_offre): string
{
    return "/autres_pages/detail_offre.php?id=$id_offre";
}

/**
 * Obtient l'URL de la page de détaille d'offre professionnel.
 * @param int $id_offre L'ID de l'offre détaillée.
 * @return string L'URL de la page de détail d'offre professionnel.
 */
function location_detail_offre_pro(int $id_offre): string
{
    return "/autres_pages/detail_offre_pro.php?id=$id_offre";
}

function location_creation_offre(): string
{
    return "/autres_pages/choix_categorie_creation_offre.php";
}

function location_detail_compte(): string
{
    return "/autres_pages/detail_compte.php";
}

function location_modifier_offre(int $id_offre): string
{
    return "/autres_pages/modif_offre.php?id_offre=$id_offre";
}

function location_modifier_compte(int $id, ?string $error = null): string
{
    return '/autres_pages/modif_compte.php?id='.$id.'&return_url=' . urlencode($_SERVER['REQUEST_URI'])
        . ($error === null ? null : '&error=' . urlencode($error));
}
