<?php

function query_offre(int $id_offre): array|false
{   
    $stmt = notfalse(db_connect()->prepare('select * from offres where id = ?'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetch();
}

function query_offres_count(?int $id_professionnel = null, ?bool $en_ligne = null): int
{
    $args = filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
    $stmt = notfalse(db_connect()->prepare('select count(*) from offres' . _where_clause('and', array_keys($args))));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function query_offres(?int $id_professionnel = null, ?bool $en_ligne = null): PDOStatement
{   
    $args = filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
    $stmt = notfalse(db_connect()->prepare('select * from offres' . _where_clause('and', array_keys($args))));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return $stmt;
}

// Insertions

/**
 * Toggles the state (en ligne/hors ligne) of an offer by adding a row in the _changement_etat table.
 *
 * @param int $id_offre The ID of the offer to toggle the state for.
 */
function offre_alterner_etat(int $id_offre): void
{
    $stmt = notfalse(db_connect()->prepare('insert into _changement_etat (id_offre) values (?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

function offre_insert_gallerie_image(int $id_offre, int $id_image)
{
    $stmt = notfalse(db_connect()->prepare('insert into _gallerie (id_offre, id_image) values (?,?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT], 2 => [$id_image, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

function offre_insert_tag(int $id_offre, string $tag)
{
    $stmt = notfalse(db_connect()->prepare('insert into _tags (id_offre, tag) values (?,?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT], 2 => [$tag, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
}

function offre_insert_tarif(int $id_offre, string $nom, float $montant)
{
    $stmt = notfalse(db_connect()->prepare('insert into _tarif (id_offre, nom, montant) values (?,?,?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT], 2 => [$nom, PDO::PARAM_STR], 3 => [$montant, PDO_PARAM_DECIMAL]]);
    notfalse($stmt->execute());
}

/**
 * Summary of offre_insert_horaire
 * @param int $id_offre
 * @param int $dow The day of the week as Sunday (0) to Saturday (6)
 * @param string $heure_debut A PostgreSQL TIME input string
 * @param string $heure_fin A PostgreSQL TIME input string.
 * @return void
 */
function offre_insert_horaire(int $id_offre, int $dow, string $heure_debut, string $heure_fin)
{
    $stmt = notfalse(db_connect()->prepare('insert into horaire_ouverture (id_offre, dow, heure_debut, heure_fin) values (?,?,?,?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT], 2 => [$dow, PDO::PARAM_INT], 3 => [$heure_debut, PDO::PARAM_STR], 4 => [$heure_fin, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
}

/**
 * Summary of offre_insert_periode
 * @param int $id_offre
 * @param string $debut_le A PostgreSQL TIMESTAMP input string.
 * @param string $fin_le A PostgreSQL TIMESTAMP input string.
 * @return void
 */
function offre_insert_periode(int $id_offre, string $debut_le, string $fin_le)
{
    $stmt = notfalse(db_connect()->prepare('insert into periode_ouverture (id_offre, debut_le, fin_le) values (?,?,?,?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT], 2 => [$debut_le, PDO::PARAM_STR], 3 => [$fin_le, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
}

function offre_args(
    int $id_adresse,
    int $id_image_principale,
    int $id_professionnel,
    string $libelle_abonnement,
    string $titre,
    string $resume,
    string $description_detaillee,
    ?string $url_site_web = null,
): array {
    return filter_null_args([
        'id_adresse' => [$id_adresse, PDO::PARAM_INT],
        'id_image_principale' => [$id_image_principale, PDO::PARAM_INT],
        'id_professionnel' => [$id_professionnel, PDO::PARAM_INT],
        'libelle_abonnement' => [$libelle_abonnement, PDO::PARAM_STR],
        'titre' => [$titre, PDO::PARAM_STR],
        'resume' => [$resume, PDO::PARAM_STR],
        'description_detaillee' => [$description_detaillee, PDO::PARAM_STR],
        'url_site_web' => [$url_site_web, PDO::PARAM_STR],
    ]);
}

/**
 * Insérer une activité.
 * @param array $offre_args
 * @param string $indication_duree ISO8601 or Postgres syntax INTERVAL string expected.
 * @param string $prestation_incluses
 * @param mixed $age_requis
 * @param mixed $prestations_non_incluses
 * @return int
 */
function insert_into_activite(
    array $offre_args,
    string $indication_duree,
    string $prestation_incluses,
    ?int $age_requis = null,
    ?string $prestations_non_incluses = null,
): int {
    $args = $offre_args + filter_null_args([
        'indication_duree' => [$indication_duree, PDO::PARAM_STR],
        'prestation_incluses' => [$prestation_incluses, PDO::PARAM_STR],
        'age_requis' => [$age_requis, PDO::PARAM_INT],
        'prestations_non_incluses' => [$prestations_non_incluses, PDO::PARAM_STR],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('activite', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function insert_into_parc_attractions(
    array $offre_args,
    int $id_image_plan,
): int {
    $args = $offre_args + filter_null_args([
        'id_image_plan' => [$id_image_plan, PDO::PARAM_INT],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('parc_attractions', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function insert_into_restaurant(
    array $offre_args,
    string $carte,
    int $richesse,
    ?bool $sert_petit_dejeuner = null,
    ?bool $sert_brunch = null,
    ?bool $sert_dejeuner = null,
    ?bool $sert_diner = null,
    ?bool $sert_boissons = null,
): int {
    $args = $offre_args + filter_null_args([
        'carte' => [$carte, PDO::PARAM_STR],
        'richesse' => [$richesse, PDO::PARAM_INT],
        'sert_petit_dejeuner' => [$sert_petit_dejeuner, PDO::PARAM_BOOL],
        'sert_brunch' => [$sert_brunch, PDO::PARAM_BOOL],
        'sert_dejeuner' => [$sert_dejeuner, PDO::PARAM_BOOL],
        'sert_diner' => [$sert_diner, PDO::PARAM_BOOL],
        'sert_boissons' => [$sert_boissons, PDO::PARAM_BOOL],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('restaurant', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

/**
 * Insérer un spectacle.
 * @param array $offre_args
 * @param string $indication_duree ISO8601 or Postgres syntax INTERVAL string expected.
 * @param int $capacite_accueil
 * @return int
 */
function insert_into_spectacle(
    array $offre_args,
    string $indication_duree,
    int $capacite_accueil,
): int {
    $args = $offre_args + filter_null_args([
        'indication_duree' => [$indication_duree, PDO::PARAM_STR],
        'capacite_accueil' => [$capacite_accueil, PDO::PARAM_INT],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('spectacle', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

/**
 * Insérer une visite.
 * @param array $offre_args
 * @param string $indication_duree ISO8601 or Postgres syntax INTERVAL string expected.
 * @return int
 */
function insert_into_visite(
    array $offre_args,
    string $indication_duree
): int {
    $args = $offre_args + filter_null_args([
        'indication_duree' => [$indication_duree, PDO::PARAM_STR],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('visite', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}
