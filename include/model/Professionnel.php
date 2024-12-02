<?php
require_once 'model/Compte.php';
// todo

/* abstract */
class Professionnel extends Compte
{
    const TABLE = 'professionnel';

    private ?int $id;
    readonly string $denomination;

    function __construct(
        ?int $id,
        string $email,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        Adresse $adresse,
        string $denomination,
    ) {
        parent::__construct(
            $id,
            $email,
            $mdp_hash,
            $nom,
            $prenom,
            $telephone,
            $adresse,
        );
        $this->denomination = $denomination;
    }

    /**
     * Récupère un professionnel de la BDD.
     * @param int $id_professionnel
     * @return Professionnel
     */
    static function from_db(int $id_professionnel): Professionnel
    {
        $stmt = notfalse(DB\connect()->prepare('select * from ' . self::TABLE . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_professionnel, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        return self::from_db_row($stmt->fetch());
    }

    /**
     * @param (string|int|bool)[] $row
     * @return Professionnel
     */
    private static function from_db_row(array $row): Professionnel
    {
        return new Professionnel(
            $row['id'],
            $row['email'],
            $row['mdp_hash'],
            $row['nom'],
            $row['prenom'],
            $row['telephone'],
            Adresse::from_db($row['id_adresse']),
            $row['denomination'],
        );
    }
}
