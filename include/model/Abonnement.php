<?php
require_once 'db.php';

final class Abonnement
{
    private const TABLE = '_abonnement';

    readonly string $libelle;
    readonly float $prix_journalier;

    private function __construct(
        string $libelle,
        float $prix_journalier,
    ) {
        $this->libelle = $libelle;
        $this->prix_journalier = $prix_journalier;
    }

    private static ?array $instances;

    static function get(string $libelle_abonnement): Abonnement
    {
        self::$instances ??= array_map(
            fn($row) => new Abonnement(
                $row['libelle'],
                parse_float($row['prix_journalier']),
            ),
            array_column(DB\connect()->query('select libelle, prix_journalier from ' . self::TABLE)->fetchAll(), null, 'libelle'),
        );
        return self::$instances[$libelle_abonnement];
    }
}
