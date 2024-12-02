<?php
require_once 'model/Compte.php';


/**
 * @inheritDoc
 * @property string $denomination
 */
// todo make this abstract 
class Professionnel extends Compte
{
    protected const FIELDS = [
        'denomination' => [null, 'denomination', PDO::PARAM_STR],
    ];

    protected ?int $id;
    protected string $denomination;

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
        $stmt = notfalse(DB\connect()->prepare('select * from ' . self::TABLE . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_professionnel, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : new Professionnel(
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

    const TABLE = 'professionnel';
}
