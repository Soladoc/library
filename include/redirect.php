<?php

function redirect_to(string $location): never
{
    header("Location: $location");
    exit;
}

function location_connexion(?string $error = null): string
{
    return '/autres_pages/connexion.php?return_url=' . urlencode($_SERVER['REQUEST_URI'])
        . ($error === null ? '&error=' . urlencode($error) : null);
}

function location_detail_offre(int $id_offre): string
{
    return "/autres_pages/detail_offre.php?id=$id_offre";
}
