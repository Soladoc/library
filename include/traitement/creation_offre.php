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
        html_error("la commune {$args['adresse_commune']} n'existe pas");
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
        $args['libelle_abonnement'],
        $args['titre'],
        $args['resume'],
        $args['description_detaillee'],
        $args['url_site_web']
    );

    $id_offre = match ($args['type_offre']) {
        'activite' => insert_into_activite(
            $offre_args,
            extract_indication_duree($args),
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
            extract_indication_duree($args),
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
            extract_indication_duree($args),
        ),
    };

    // Gallerie
    foreach (soa_to_aos($args['file_gallerie']) as $img) {
        offre_insert_gallerie_image($id_offre, move_uploaded_image($img));
    }

    // Tags
    foreach (array_keys($args['tags']) as $tag) {
        offre_insert_tag($id_offre, $tag);
    }

    // Tarifs
    foreach (soa_to_aos($args['tarifs']) as $tarif) {
        offre_insert_tarif($id_offre, $tarif['nom'], $tarif['montant']);
    }

    // Horaires
    foreach ($args['horaires'] as $dow => $horaires) {
        foreach (soa_to_aos($horaires) as $horaire) {
            offre_insert_horaire($id_offre, $dow, $horaire['debut'], $horaire['fin']);
        }
    }

    // Périodes
    foreach (soa_to_aos($args['periodes']) as $periode) {
        offre_insert_periode($id_offre, $periode['debut'], $periode['fin']);
    }

}, function () {
    global $uploaded_files;
    array_walk($uploaded_files, unlink(...));
});

function extract_indication_duree(array $args): string {
    return make_interval($args['indication_duree_jours'], $args['indication_duree_heures'], $args['indication_duree_minutes']);
}
