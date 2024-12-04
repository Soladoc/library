<?php
namespace DB;

require_once 'db.php';
require_once 'util.php';

use PDO, Iterator;

function query_offre(int $id_offre): array|false
{
    $stmt = notfalse(connect()->prepare('select * from offres where id = ?'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetch();
}

function query_supprime_avis(int $id_avis): bool
{
    $stmt = notfalse(connect()->prepare('DELETE FROM _avis WHERE id = ?'));
    $stmt->bindValue(1, $id_avis, PDO::PARAM_INT);
    return $stmt->execute();
}



function query_offres_count(?int $id_professionnel = null, ?bool $en_ligne = null): int
{
    $args = filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
    $stmt = notfalse(connect()->prepare('select count(*) from offres' . where_clause(BoolOperator::AND, array_keys($args))));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function query_offres(?int $id_professionnel = null, ?bool $en_ligne = null): Iterator
{
    $args = filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
    $stmt = notfalse(connect()->prepare('select * from offres' . where_clause(BoolOperator::AND, array_keys($args))));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return $stmt->getIterator();
}

function offre_insert_galerie_image(int $id_offre, int $id_image)
{
    $stmt = notfalse(connect()->prepare('insert into _galerie (id_offre, id_image) values (?,?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT], 2 => [$id_image, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

function offre_insert_tag(int $id_offre, string $tag)
{
    $stmt = notfalse(connect()->prepare('insert into _tags (id_offre, tag) values (?,?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT], 2 => [$tag, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
}

function offre_insert_tarif(int $id_offre, string $nom, float $montant)
{
    $stmt = notfalse(connect()->prepare('insert into tarif (id_offre, nom, montant) values (?,?,?)'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT], 2 => [$nom, PDO::PARAM_STR], 3 => [$montant, PDO_PARAM_FLOAT]]);
    notfalse($stmt->execute());
}

/**
 * Insère les horaires d'ouverture hebdomadaires d'une offre.
 * @param int $id_offre
 * @param int $dow The day of the week as Sunday (0) to Saturday (6)
 * @param \MultiRange<\Time> $horaires Les horaires pour ce jour
 * @return void
 */
function offre_insert_ouverture_hebdomadaire(int $id_offre, int $dow, \MultiRange $horaires)
{
    $stmt = notfalse(connect()->prepare('insert into _ouverture_hebdomadaire (id_offre, dow, horaires) values (?,?,?)'));
    bind_values($stmt, [
        1 => [$id_offre, PDO::PARAM_INT],
        2 => [$dow, PDO::PARAM_INT],
        3 => [$horaires, PDO::PARAM_STR],
    ]);
    notfalse($stmt->execute());
}

/**
 * Génère les arguments communs pour l'insertion d'offre.
 * @param int $id_adresse
 * @param int $id_image_principale
 * @param int $id_professionnel
 * @param string $libelle_abonnement
 * @param string $titre
 * @param string $resume
 * @param string $description_detaillee
 * @param \MultiRange<\FiniteTimestamp>
 * @param ?string $url_site_web
 * @return array Les arguments communs pour l'insertion d'offre.
 */
function offre_args(
    int $id_adresse,
    int $id_image_principale,
    int $id_professionnel,
    string $libelle_abonnement,
    string $titre,
    string $resume,
    string $description_detaillee,
    \MultiRange $periodes_ouverture,
    ?string $url_site_web,
): array {
    return filter_null_args([
        'id_adresse' => [$id_adresse, PDO::PARAM_INT],
        'id_image_principale' => [$id_image_principale, PDO::PARAM_INT],
        'id_professionnel' => [$id_professionnel, PDO::PARAM_INT],
        'libelle_abonnement' => [$libelle_abonnement, PDO::PARAM_STR],
        'titre' => [$titre, PDO::PARAM_STR],
        'resume' => [$resume, PDO::PARAM_STR],
        'description_detaillee' => [$description_detaillee, PDO::PARAM_STR],
        'periodes_ouverture' => [$periodes_ouverture, PDO::PARAM_STR],
        'url_site_web' => [$url_site_web, PDO::PARAM_STR],
    ]);
}

/**
 * Insérer une activité.
 * @param array $offre_args Les arguments communs pour les offres générés par la fonction `DB\offre_args`. Les arguments communs pour les offres générés par la fonction `DB\offre_args`
 * @param string $indication_duree ISO8601 or Postgres syntax INTERVAL string expected.
 * @param string $prestations_incluses
 * @param mixed $age_requis
 * @param mixed $prestations_non_incluses
 * @return int L'ID de l'activté insérée.
 */
function insert_into_activite(
    array $offre_args,
    string $indication_duree,
    string $prestations_incluses,
    ?int $age_requis = null,
    ?string $prestations_non_incluses = null,
): int {
    $args = $offre_args + filter_null_args([
        'indication_duree' => [$indication_duree, PDO::PARAM_STR],
        'prestations_incluses' => [$prestations_incluses, PDO::PARAM_STR],
        'age_requis' => [$age_requis, PDO::PARAM_INT],
        'prestations_non_incluses' => [$prestations_non_incluses, PDO::PARAM_STR],
    ]);
    $stmt = insert_into('activite', $args, ['id']);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

/**
 * Insérer un parc d'attractions.
 * @param array $offre_args Les arguments communs pour les offres générés par la fonction `DB\offre_args`.
 * @param int $id_image_plan
 * @return int L'ID du parc d'attractions inséré.
 */
function insert_into_parc_attractions(
    array $offre_args,
    int $id_image_plan,
): int {
    $args = $offre_args + filter_null_args([
        'id_image_plan' => [$id_image_plan, PDO::PARAM_INT],
    ]);
    $stmt = insert_into('parc_attractions', $args, ['id']);
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

/**
 * Insérer un restaurant.
 * @param array $offre_args Les arguments communs pour les offres générés par la fonction `DB\offre_args`.
 * @param string $carte
 * @param int $richesse
 * @param ?bool $sert_petit_dejeuner
 * @param ?bool $sert_brunch
 * @param ?bool $sert_dejeuner
 * @param ?bool $sert_diner
 * @param ?bool $sert_boissons
 * @return int
 */
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
    $stmt = insert_into('restaurant', $args, ['id']);
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

/**
 * Insérer un spectacle.
 * @param array $offre_args Les arguments communs pour les offres générés par la fonction `DB\offre_args`.
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
    $stmt = insert_into('spectacle', $args, ['id']);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

/**
 * Insérer une visite.
 * @param array $offre_args Les arguments communs pour les offres générés par la fonction `DB\offre_args`.
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
    $stmt = insert_into('visite', $args, ['id']);
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function query_activite(int $id_activite): array|false
{
    $stmt = notfalse(connect()->prepare('select * from _activite where id = ?'));
    bind_values($stmt, [1 => [$id_activite, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetch();
}