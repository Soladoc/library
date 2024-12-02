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
     * @return Professionnel|false
     */
    static function from_db(int $id_professionnel): Professionnel|false
    {
        $stmt = notfalse(DB\connect()->prepare('select email,mdp_hash,nom,prenom,telephone,denomination,a.* from '
            . self::TABLE . ' inner join ' . Adresse::TABLE . ' a on a.id = id_adresse where id = ?'));
        DB\bind_values($stmt, [1 => [$id_professionnel, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        if ($row === false) return false;
        return self::from_db_row($row);
    }

    /**
     * @param (string|int|bool)[] $row
     * @return Professionnel
     */
    static function from_db_row(array $row, string $id_column = 'id'): Professionnel
    {
        return new Professionnel(
            $row[$id_column],
            $row['email'],
            $row['mdp_hash'],
            $row['nom'],
            $row['prenom'],
            $row['telephone'],
            Adresse::from_db_row($row, 'id_adresse'),
            $row['denomination'],
        );
    }
}
