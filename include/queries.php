<?php
namespace DB;

use PDO;

require_once 'db.php';

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
