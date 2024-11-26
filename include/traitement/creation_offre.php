<?php
require_once "model/MultiRange.php";
require_once "model/NonEmptyRange.php";
require_once "model/Time.php";
require_once "model/Timestamp.php";

// Conserve les images uploadées durant cette transaction pour les supprimer en cas d'erreur. Comme ça on ne pollue pas le dossier.
$uploaded_files = [];

function move_uploaded_image(array $file)
{
    global $uploaded_files;
    [$uploaded_files[], $id_image] = DB\insert_uploaded_image($file);
    return $id_image;
}

/*?>
<pre><samp><?= htmlspecialchars(print_r($_GET, true)) ?></samp></pre>
<pre><samp><?= htmlspecialchars(print_r($_POST, true)) ?></samp></pre>
<pre><samp><?= htmlspecialchars(print_r($_FILES, true)) ?></samp></pre>
<?php*/

DB\transaction(function () {
    global $args, $id_professionnel, $id_offre;

    // Récupérer la commune
    // todo: make this better (by inputting either nom or code postal)
    $commune = single_or_default(DB\query_communes($args['adresse_commune']));
    if ($commune === null) {
        html_error("la commune {$args['adresse_commune']} n'existe pas");
    }
    // Insérer l'adresse
    // todo: adresses localisées
    $id_adresse = DB\insert_adresse(
        $commune['code'],
        $commune['numero_departement'],
        $args['adresse_numero_voie'],
        $args['adresse_complement_numero'],
        $args['adresse_nom_voie'],
        $args['adresse_localite'],
        $args['adresse_precision_int'],
        $args['adresse_precision_ext'],
    );

    // Périodes d'ouverture
    $periodes_ouverture = new MultiRange(array_map(
        fn($debut, $fin) => new NonEmptyRange(true, Timestamp::parse($debut), Timestamp::parse($fin), false),
        $args['periodes']['debut'],
        $args['periodes']['fin']
    ));

    // Insérer l'offre
    $offre_args = DB\offre_args(
        $id_adresse,
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

    // Gallerie
    foreach (soa_to_aos($args['file_gallerie']) as $img) {
        DB\offre_insert_gallerie_image($id_offre, move_uploaded_image($img));
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
