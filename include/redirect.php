<?php

function redirect_to(string $location): never
{
    header("Location: $location");
    exit;
}

function location_connexion(?string $error = null): string
{
    return '/autres_pages/connexion.php?return_url=' . urlencode($_SERVER['REQUEST_URI'])
        . ($error === null ? null : '&error=' . urlencode($error));
}

function location_detail_offre(int $id_offre): string
{
    return "/autres_pages/detail_offre.php?id=$id_offre";
}


function location_modif_compte(?string $error = null,int $id): string
{
    return '/autres_pages/modif_compte.php?id='.$id.'?return_url=' . urlencode($_SERVER['REQUEST_URI'])
        . ($error === null ? null : '&error=' . urlencode($error));
}