<?php
require_once 'db.php';

final class Abonnement
{
    private const TABLE = '_abonnement';

    private function __construct(
        string $libelle,
        float $prix,
    ) {
        $this->libelle = $libelle;
        $this->prix = $prix;
    }

    private static ?array $instances;

    static function from_db(string $libelle_abonnement): Abonnement
    {
        self::$instances ??= array_map(
            fn($row) => new Abonnement($row['libelle_abonnement'], $row['prix']),
            array_column(DB\connect()->query('select * from ' . self::TABLE)->fetchAll(), null, 'libelle_abonnement'),
        );
        return self::$instances[$libelle_abonnement];
    }
}
