<?php
require_once 'db.php';

final class ProfessionnelPrive extends Professionnel
{
    const TABLE = 'pro_prive';

    protected const FIELDS = parent::FIELDS + [
        'siren' => [[null, 'siren', PDO::PARAM_STR]],
    ];

    protected string $siren;

    function __construct(
        string $email,
        ?int $id,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        Adresse $adresse,
        string $denomination,
        string $siren,
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
        $this->siren = $siren;
    }

    static function exists(int $id_pro_prive): bool
    {
        $stmt = notfalse(DB\connect()->prepare('select ? in (select id from ' . self::TABLE . ')'));
        DB\bind_values($stmt, [1 => [$id_pro_prive, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        return $stmt->fetchColumn();
    }
}
