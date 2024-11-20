<?php

// Conserve les images uploadées durant cette transaction pour les supprimer en cas d'erreur. Comme ça on ne pollue pas le dossier.
$uploaded_files = [];

function move_uploaded_image(array $file)
{
    global $uploaded_files;
    [$uploaded_files[], $id_image] = insert_uploaded_image($file);
    return $id_image;
}

transaction(function () {
    global $args, $id_professionnel;

    // Récupérer la commune
    // todo: make this better (by inputting either nom or code postal)
    $commune = single_or_default(query_communes($args['adresse_commune']));
    if ($commune === null) {
        html_error("la commune '$commune' n'existe pas");
    }
    // Insérer l'adresse
    // todo: adresses localisées
    $id_adresse = insert_into_adresse(
        $commune['code'],
        $commune['numero_departement'],
        $args['adresse_numero_voie'],
        $args['adresse_complement_numero'],
        $args['adresse_nom_voie'],
        $args['adresse_localite'],
        $args['adresse_precision_int'],
        $args['adresse_precision_ext'],
    );

    // Insérer l'offre
    $offre_args = offre_args(
        $id_adresse,
        move_uploaded_image($args['file_image_principale']),
        $id_professionnel,
        'gratuit',  // todo: standard et premium
        $args['titre'],
        $args['resume'],
        $args['description_detaillee'],
        $args['url_site_web']
    );

    $id_offre = match ($args['type_offre']) {
        'activite' => insert_into_activite(
            $offre_args,
            $args['indication_duree'],
            $args['prestations_incluses'],
            $args['age_requis'],
            $args['prestations_non_incluses'],
        ),
        'parc-attractions' => insert_into_parc_attractions(
            $offre_args,
            move_uploaded_image($args['file_image_plan']),
        ),
        'spectacle' => insert_into_spectacle(
            $offre_args,
            $args['indication_duree'],
            $args['capacite_accueil'],
        ),
        'restaurant' => insert_into_restaurant(
            $offre_args,
            $args['carte'],
            $args['richesse'],
            $args['sert_petit_dejeuner'],
            $args['sert_brunch'],
            $args['sert_dejeuner'],
            $args['sert_diner'],
            $args['sert_boissons'],
        ),
        'visite' => insert_into_visite(
            $offre_args,
            $args['indication_duree'],
        ),
    };

    foreach (soa_to_aos($args['file_gallerie']) as $img) {
        insert_into_gallerie($id_offre, move_uploaded_image($img));
    }
}, function () {
    global $uploaded_files;
    array_walk($uploaded_files, unlink(...));
});
