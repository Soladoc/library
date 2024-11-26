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
 * @return string L'URL de la page de connection.
 */
function location_connexion(?string $error = null): string
{
    return '/autres_pages/connexion.php?return_url=' . urlencode($_SERVER['REQUEST_URI'])
        . ($error === null ? null : '&error=' . urlencode($error));
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

function location_detail_compte(int $id_compte): string
{
    return "/autres_pages/detail_compte.php?id=$id_compte";
}

function location_modifier_offre(int $id_offre): string
{
    return "/autres_pages/modifier_offre.php?id_offre=$id_offre";
}

function location_modif_compte(int $id, ?string $error = null): string
{
    return '/autres_pages/modif_compte.php?id='.$id.'&return_url=' . urlencode($_SERVER['REQUEST_URI'])
        . ($error === null ? null : '&error=' . urlencode($error));
}