<?php

require_once 'db.php';

final class Avis
{
    private const TABLE = 'avis';

    /**
     * Obtient le nombre d'avis d'une offre.
     * @param int $id_offre L'ID de l'offre dont on souhaite compter les avis.
     * @return int Le nombre d'avis commentant l'offre d'ID $id_offre.
     */
    static function get_count(int $id_offre): int
    {
        $stmt = notfalse(DB\connect()->prepare('select count(*) from ' . self::TABLE . ' where id_offre = ?'));
        DB\bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        return notfalse($stmt->fetchColumn());
    }
}
