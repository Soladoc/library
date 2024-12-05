<?php
namespace DB;

require_once 'db.php';
require_once 'util.php';

use PDO, Iterator;

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

