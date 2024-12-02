<?php
require_once "model/MultiRange.php";
require_once "model/NonEmptyRange.php";
require_once "model/Time.php";
require_once "model/FiniteTimestamp.php";
require_once "model/Adresse.php";

// Conserve les images uploadées durant cette transaction pour les supprimer en cas d'erreur. Comme ça on ne pollue pas le dossier.
$uploaded_files = [];

function move_uploaded_image(array $file)
{
    global $uploaded_files;
    [$uploaded_files[], $id_image] = DB\insert_uploaded_image($file);
    return $id_image;
}

DB\transaction(function () {
    global $args, $id_professionnel, $id_offre, $input_adresse;

    /** @var Adresse */
    $adresse = $input_adresse->get($_POST);


    // Insérer l'adresse
    // todo: adresses localisées
    $adresse->push_to_db();

    // Périodes d'ouverture
    $periodes_ouverture = new MultiRange(array_map(
        fn($debut, $fin) => new NonEmptyRange(true, FiniteTimestamp::parse($debut), FiniteTimestamp::parse($fin), false),
        $args['periodes']['debut'],
        $args['periodes']['fin']
    ));

    // Insérer l'offre
    $offre_args = DB\offre_args(
        $adresse->id,
        move_uploaded_image($args['file_image_principale']),
        $id_professionnel,
        $args['libelle_abonnement'],
        $args['titre'],
        $args['resume'],
        $args['description_detaillee'],
        $periodes_ouverture,
        $args['url_site_web']
    );

    $id_offre = match ($args['type_offre']) {
        'activité' => DB\insert_into_activite(
            $offre_args,
            extract_indication_duree($args),
            $args['prestations_incluses'],
            $args['age_requis'],
            $args['prestations_non_incluses'],
        ),
        'parc d\'attractions' => DB\insert_into_parc_attractions(
            $offre_args,
            move_uploaded_image($args['file_image_plan']),
        ),
        'spectacle' => DB\insert_into_spectacle(
            $offre_args,
            extract_indication_duree($args),
            $args['capacite_accueil'],
        ),
        'restaurant' => DB\insert_into_restaurant(
            $offre_args,
            $args['carte'],
            $args['richesse'],
            $args['sert_petit_dejeuner'],
            $args['sert_brunch'],
            $args['sert_dejeuner'],
            $args['sert_diner'],
            $args['sert_boissons'],
        ),
        'visite' => DB\insert_into_visite(
            $offre_args,
            extract_indication_duree($args),
        ),
    };

    // Galerie
    foreach (soa_to_aos($args['file_galerie']) as $img) {
        DB\offre_insert_galerie_image($id_offre, move_uploaded_image($img));
    }

    // Tags
    foreach (array_keys($args['tags']) as $tag) {
        DB\offre_insert_tag($id_offre, $tag);
    }

    // Tarifs
    foreach (soa_to_aos($args['tarifs']) as $tarif) {
        DB\offre_insert_tarif($id_offre, $tarif['nom'], $tarif['montant']);
    }

    // Horaires
    foreach ($args['horaires'] as $dow => $horaires) {
        DB\offre_insert_ouverture_hebdomadaire($id_offre, $dow, new MultiRange(array_map(
            fn($debut, $fin) => new NonEmptyRange(true, Time::parse($debut), Time::parse($fin), false),
            $horaires['debut'],
            $horaires['fin']
        )));
    } 
}, function () {
    global $uploaded_files;
    foreach ($uploaded_files as $file);{
        unlink($file);
    }
});

// Rediriger vers la page de détaille de l'offre en cas de succès.
// En cas d'échec, l'exception est jetée par DB\transaction(), donc on attenti pas cette ligne.
redirect_to(location_detail_offre_pro($id_offre));

function extract_indication_duree(array $args): string
{
    return DB\make_interval($args['indication_duree_jours'], $args['indication_duree_heures'], $args['indication_duree_minutes']);
}
