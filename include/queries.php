<?php

require_once 'db.php';
require_once 'queries/util.php';

require_once 'queries/offre.php';

// Selections

// Function selections

function make_interval(int $days, int $hours, int $mins)
{
    $stmt = notfalse(db_connect()->prepare('select make_interval(days => ?, hours => ?, mins => ?)'));
    bind_values($stmt, [1 => [$days, PDO::PARAM_INT], 2 => [$hours, PDO::PARAM_INT], 3 => [$mins, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function query_avis_count(int $id_offre): int
{
    $stmt = notfalse(db_connect()->prepare('select count(*) from avis where id_offre = ?'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

function query_image(int $id_image): array
{
    $stmt = notfalse(db_connect()->prepare('select * from _image where id = ?'));
    bind_values($stmt, [1 => [$id_image, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return notfalse($stmt->fetch());
}

/**
 * Récupère les images de la gallerie d'une offre
 * @return array<int> Un tableau d'id d'images. Utilise query_image pour retrouver les infos sur l'image.
 */
function query_gallerie(int $id_offre): array
{
    $stmt = notfalse(db_connect()->prepare('select id_image from _gallerie where id_offre = ?'));
    bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return array_map(fn($row) => $row['id_image'], $stmt->fetchAll());
}

function query_adresse(int $id_adresse): array|false
{
    $stmt = notfalse(db_connect()->prepare('select * from _adresse where id = ?'));
    bind_values($stmt, [1 => [$id_adresse, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetch();
}

function query_commune(int $code, string $numero_departement): array
{
    $stmt = notfalse(db_connect()->prepare('select * from _commune where code = ? and numero_departement = ?'));
    bind_values($stmt, [1 => [$code, PDO::PARAM_INT], 2 => [$numero_departement, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
    return notfalse($stmt->fetch());
}

function query_codes_postaux(int $code_commune, string $numero_departement): array
{
    $stmt = notfalse(db_connect()->prepare('select code_postal from _code_postal where code_commune = ? and numero_departement = ?'));
    bind_values($stmt, [1 => [$code_commune, PDO::PARAM_INT], 2 => [$numero_departement, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
    return array_map(fn($row) => $row['code_postal'], $stmt->fetchAll());
}

function query_membre(string $email_or_pseudo): array|false
{
    // We know at symbols are not allowed in pseudonyms so if there is one, the user meant to connact with their email.
    $stmt = notfalse(db_connect()->prepare('select * from membre where ' . (str_contains($email_or_pseudo, '@') ? 'email' : 'pseudo') . ' = ? limit 1'));
    notfalse($stmt->execute([$email_or_pseudo]));
    return $stmt->fetch();
}

function query_professionnel(string $email): array|false
{
    $stmt = notfalse(db_connect()->prepare('select * from professionnel where email = ?'));
    notfalse($stmt->execute([$email]));
    return $stmt->fetch();
}

function query_compte_membre(int $id): array|false
{
    $stmt = notfalse(db_connect()->prepare('select * from membre where id = ?'));
    bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetch();
}

function query_compte_professionnel(int $id): array|false
{
    $stmt = notfalse(db_connect()->prepare('select * from professionnel where id = ?'));
    bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetch();
}

function query_get_siren(int $id_compte): int
{
    $stmt = notfalse(db_connect()->prepare('select siren from pro_prive where id = ?'));
    bind_values($stmt, [1 => [$id_compte, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

// Parameterized selections

function query_communes(?string $nom = null): array
{
    $args = filter_null_args(['nom' => [$nom, PDO::PARAM_STR]]);
    $stmt = notfalse(db_connect()->prepare('select * from _commune' . _where_clause('and', array_keys($args))));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchAll());
}

function query_avis(?int $id_membre_auteur = null, ?int $id_offre = null): array
{
    $args = filter_null_args(['id_membre_auteur' => [$id_membre_auteur, PDO::PARAM_INT], 'id_offre' => [$id_offre, PDO::PARAM_INT]]);
    $stmt = notfalse(db_connect()->prepare('select * from avis ' . _where_clause('and', array_keys($args))));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return $stmt->fetchAll();
}

// Update-----------------------------------------------------------------------------------------------------------

function uptate_mdp(int $id_compte, $new_mdp): void
{
    $stmt = notfalse(db_connect()->prepare('UPDATE _compte SET mdp_hash = ? WHERE id = ?;'));
    bind_values($stmt, [$new_mdp, $id_compte]);
    notfalse($stmt->execute());
}

function uptate_nom(int $id_compte, $new_nom): void
{
    $stmt = notfalse(db_connect()->prepare('UPDATE _compte SET nom = ? WHERE id = ?;'));
    bind_values($stmt, [$new_nom, $id_compte]);
    notfalse($stmt->execute());
}

function uptate_email(int $id_compte, $new_email): void
{
    $stmt = notfalse(db_connect()->prepare('UPDATE _compte SET email = ? WHERE id = ?;'));
    bind_values($stmt, [$new_email, $id_compte]);
    notfalse($stmt->execute());
}

function uptate_prenom(int $id_compte, $new_prenom): void
{
    $stmt = notfalse(db_connect()->prepare('UPDATE _compte SET prenom = ? WHERE id = ?;'));
    bind_values($stmt, [$new_prenom, $id_compte]);
    notfalse($stmt->execute());
}

function uptate_telephone(int $id_compte, $new_telephone): void
{
    $stmt = notfalse(db_connect()->prepare('UPDATE _compte SET telephone = ? WHERE id = ?;'));
    bind_values($stmt, [$new_telephone, $id_compte]);
    notfalse($stmt->execute());
}

// membre
function uptate_pseudo(int $id_compte, $new_pseudo): void
{
    $stmt = notfalse(db_connect()->prepare('UPDATE membre SET pseudo = ? WHERE id = ?;'));
    bind_values($stmt, [$new_pseudo, $id_compte]);
    notfalse($stmt->execute());
}

// professionnel
function uptate_denomination(int $id_compte, $new_denomination): void
{
    $stmt = notfalse(db_connect()->prepare('UPDATE professionnel SET denomination = ? WHERE id = ?;'));
    bind_values($stmt, [$new_denomination, $id_compte]);
    notfalse($stmt->execute());
}

function update_siren(int $id_compte, $new_siren): void
{
    $stmt = notfalse(db_connect()->prepare('UPDATE _prive SET siren = ? WHERE id = ?;'));
    bind_values($stmt, [$new_siren, $id_compte]);
    notfalse($stmt->execute());
}

// Insertions---------------------------------------------------------------------------------------------------------

/**
 * Insère une nouvelle  adresse dans la BDD et retourne son ID.
 *
 * @param int $code_commune
 * @param int $numero_departement
 * @param int|null $numero_voie
 * @param string|null $complement_numero
 * @param string|null $nom_voie
 * @param string|null $localite
 * @param string|null $precision_int
 * @param string|null $precision_ext
 * @param float|null $latitude
 * @param float|null $longitude
 * @return int L'ID de l'adresse nouvellement insérée.
 */
function insert_adresse(
    int $code_commune,
    int $numero_departement,
    ?int $numero_voie = null,
    ?string $complement_numero = null,
    ?string $nom_voie = null,
    ?string $localite = null,
    ?string $precision_int = null,
    ?string $precision_ext = null,
    ?float $latitude = null,
    ?float $longitude = null,
): int {
    $args = filter_null_args([
        'code_commune' => [$code_commune, PDO::PARAM_INT],
        'numero_departement' => [$numero_departement, PDO::PARAM_INT],
        'numero_voie' => [$numero_voie, PDO::PARAM_INT],
        'complement_numero' => [$complement_numero, PDO::PARAM_INT],
        'nom_voie' => [$nom_voie, PDO::PARAM_STR],
        'localite' => [$localite, PDO::PARAM_STR],
        'precision_int' => [$precision_int, PDO::PARAM_STR],
        'precision_ext' => [$precision_ext, PDO::PARAM_STR],
        'latitude' => [$latitude, PDO_PARAM_DECIMAL],
        'longitude' => [$longitude, PDO_PARAM_DECIMAL],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('_adresse', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return notfalse($stmt->fetchColumn());
}

/**
 * Inserts an image into the database and returns the filename and ID of the inserted image.
 *
 * @param array $img An associative array containing the image data, with keys 'size', 'type' and 'tmp_name'.
 * @param ?string $legende The image legend (optional).
 * @return array An array containing the filename and the ID of the inserted image.
 */
function insert_uploaded_image(array $img, ?string $legende = null): array
{
    $args = filter_null_args([
        'taille' => [$img['size'], PDO::PARAM_INT],
        'mime_subtype' => [$img['type'], PDO::PARAM_STR],
        'legende' => [$legende, PDO::PARAM_STR],
    ]);
    $stmt = notfalse(db_connect()->prepare(_insert_into_returning_id('_image', $args)));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    $id_image = notfalse($stmt->fetchColumn());

    $filename = __DIR__ . "/../images_utilisateur/$id_image.{$img['type']}";
    notfalse(move_uploaded_file($img['tmp_name'], $filename));
    return [$filename, $id_image];
}

/**
 * Checks if a given private professional ID exists in the database.
 *
 * @param int $id_pro_prive The ID of the private professional to check.
 * @return bool True if the private professional ID exists, false otherwise.
 */
function exists_pro_prive(int $id_pro_prive): bool {
    $stmt = notfalse(db_connect()->prepare('select ? in (select id from pro_prive)'));
    bind_values($stmt, [1 => [$id_pro_prive, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->execute();
}