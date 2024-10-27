<?php

require_once 'db.php';

function query_offres_nb_avis(int $id_offre): int
{
    $stmt = db_connect()->prepare('select count(*) from avis where id_offre = ?');
    $stmt->execute([$id_offre]);
    return $stmt->fetchColumn();
}

function query_offres_count(int $id_professionnel): int
{
    $stmt = db_connect()->prepare('select count(*) from offres where id_professionnel = ?');
    $stmt->execute([$id_professionnel]);
    return $stmt->fetchColumn();
}

function query_offres_count_en_ligne(int $id_professionnel): int
{
    $stmt = db_connect()->prepare('select count(*) from offres where id_professionnel = ? and en_ligne');
    $stmt->execute([$id_professionnel]);
    return $stmt->fetchColumn();
}

function query_offres(int $id_professionnel, bool $en_ligne): PDOStatement
{
    $stmt = db_connect()->prepare('select * from offres where id_professionnel = ? and en_ligne = ?');
    $stmt->execute([$en_ligne, $id_professionnel]);
    return $stmt;
}
