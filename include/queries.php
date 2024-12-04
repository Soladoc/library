<?php
namespace DB;
use PDO;
require_once 'db.php';

require_once 'queries/offre.php';

// Selections

function query_images(): \Iterator
{
    $stmt = notfalse(connect()->prepare('select * from _image'));
    notfalse($stmt->execute());
    return $stmt->getIterator();
}

function query_adresse(int $id_adresse): array|false
{
    $stmt = notfalse(connect()->prepare('select * from _adresse where id = ?'));
    bind_values($stmt, [1 => [$id_adresse, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetch();
}

function query_commune(int $code, string $numero_departement): array
{
    $stmt = notfalse(connect()->prepare('select * from _commune where code = ? and numero_departement = ?'));
    bind_values($stmt, [1 => [$code, PDO::PARAM_INT], 2 => [$numero_departement, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
    return notfalse($stmt->fetch());
}

function query_codes_postaux(int $code_commune, string $numero_departement): array
{
    $stmt = notfalse(connect()->prepare('select code_postal from _code_postal where code_commune = ? and numero_departement = ?'));
    bind_values($stmt, [1 => [$code_commune, PDO::PARAM_INT], 2 => [$numero_departement, PDO::PARAM_STR]]);
    notfalse($stmt->execute());
    return array_map(fn($row) => $row['code_postal'], $stmt->fetchAll());
}

function query_tags(int $id): array|false
{
    $stmt = notfalse(connect()->prepare('select tag from _tags where id_offre = ?'));
    bind_values($stmt, [1 => [$id, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Parameterized selections

function query_avis(?int $id_membre_auteur = null, ?int $id_offre = null): array
{
    $args = filter_null_args(['id_membre_auteur' => [$id_membre_auteur, PDO::PARAM_INT], 'id_offre' => [$id_offre, PDO::PARAM_INT]]);
    $stmt = notfalse(connect()->prepare('select * from avis ' . where_clause(BoolOperator::AND, clauses: array_keys($args))));
    bind_values($stmt, $args);
    notfalse($stmt->execute());
    return $stmt->fetchAll();
}

function query_select_offre_motcle(string $motcle):array{

    $mots=explode(" ",trim($motcle));
    for($i=0; $i<count($mots); $i++) {
        $mc[$i] = "titre ilike '%".$mots[$i]."%'";
    }
    $stmt = notfalse(connect()->prepare('select * from offres where ' .implode(" and ", $mc)));
    // var_dump($stmt->queryString);
    notfalse($stmt->execute());
    return $stmt->fetchAll();
}

// Update-----------------------------------------------------------------------------------------------------------

function query_update_mdp(int $id_compte, string $new_mdp): void
{
    $stmt = notfalse(connect()->prepare('UPDATE _compte SET mdp_hash = ? WHERE id = ?;'));
    bind_values($stmt, [1 => [$new_mdp, PDO::PARAM_STR], 2 => [$id_compte, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

function query_update_Nom(int $id_compte, $new_nom): void
{
    $stmt = notfalse(connect()->prepare('UPDATE _compte SET nom = ? WHERE id = ?;'));
    bind_values($stmt, [1 => [$new_nom, PDO::PARAM_STR], 2 => [$id_compte, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

function query_update_email(int $id_compte, $new_email): void
{
    $stmt = notfalse(connect()->prepare('UPDATE _compte SET email = ? WHERE id = ?;'));
    bind_values($stmt, [1 => [$new_email, PDO::PARAM_STR], 2 => [$id_compte, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

function query_update_prenom(int $id_compte, $new_prenom): void
{
    $stmt = notfalse(connect()->prepare('UPDATE _compte SET prenom = ? WHERE id = ?;'));
    bind_values($stmt, [1 => [$new_prenom, PDO::PARAM_STR], 2 => [$id_compte, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

function query_update_telephone(int $id_compte, $new_telephone): void
{
    $stmt = notfalse(connect()->prepare('UPDATE _compte SET telephone = ? WHERE id = ?;'));
    bind_values($stmt, [1 => [$new_telephone, PDO::PARAM_STR], 2 => [$id_compte, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

// membre
function query_update_pseudo(int $id_compte, $new_pseudo): void
{
    $stmt = notfalse(connect()->prepare('UPDATE membre SET pseudo = ? WHERE id = ?;'));
    bind_values($stmt, [1 => [$new_pseudo, PDO::PARAM_STR], 2 => [$id_compte, PDO::PARAM_INT]]);
}

// professionnel
function query_update_denomination(int $id_compte, $new_denomination): void
{
    $stmt = notfalse(connect()->prepare('UPDATE professionnel SET denomination = ? WHERE id = ?;'));
    bind_values($stmt, [1 => [$new_denomination, PDO::PARAM_STR], 2 => [$id_compte, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}

function query_update_siren(int $id_compte, $new_siren): void
{
    $stmt = notfalse(connect()->prepare('UPDATE _prive SET siren = ? WHERE id = ?;'));
    bind_values($stmt, [1 => [$new_siren, PDO::PARAM_STR], 2 => [$id_compte, PDO::PARAM_INT]]);
    notfalse($stmt->execute());
}
