<?php
require_once 'db.php';

final class Offre
{
    private const TABLE = 'offres';

    readonly string $titre;
    readonly string $resume;
    readonly string $description_detaillee;
    readonly string $categorie;
    readonly Adresse $adresse;
    readonly Image $image_principale;
    readonly int $id;
    readonly int $nb_avis;
    readonly float $note_moyenne;

    static function get_offres_a_la_une(): array
    {
        $stmt = notfalse(DB\connect()->prepare('select * from offres where note_moyenne = 5'));
        notfalse($stmt->execute());
        return $stmt->fetchAll();
    }

    static function get_offres(?int $id_professionnel = null, ?bool $en_ligne = null): array
    {
        $args = DB\filter_null_values(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
        $stmt = notfalse(DB\connect()->prepare('select * from offres' . DB\where_clause(DB\BoolOperator::AND, array_keys($args))));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        return $stmt->fetchAll();
    }
}
