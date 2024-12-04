<?php
require_once 'db.php';

final class ProfessionnelPrive extends Professionnel
{
    protected const FIELDS = parent::FIELDS + [
        'siren' => [[null, 'siren', PDO::PARAM_STR]],
    ];

    function __construct(
        string $email,
        ?int $id,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        Adresse $adresse,
        string $denomination,
        readonly string $siren,
    ) {
        parent::__construct(
            $id,
            $email,
            $mdp_hash,
            $nom,
            $prenom,
            $telephone,
            $adresse,
            $denomination,
        );
    }

    static function exists(int $id_pro_prive): bool
    {
        $stmt = notfalse(DB\connect()->prepare('select ? in (select id from ' . self::TABLE . ')'));
        DB\bind_values($stmt, [1 => [$id_pro_prive, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        return $stmt->fetchColumn();
    }

    const TABLE = 'pro_prive';
}
