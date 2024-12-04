<?php
require_once 'model/Compte.php';

/**
 * @inheritDoc
 * @property-read ?string $secteur `null` si ce professionnel n'existe pas dans la BDD.
 */

/* abstract */
class Professionnel extends Compte
{
    protected static function fields()
    {
        return parent::fields() + [
            'denomination' => [null, 'denomination', PDO::PARAM_STR],
        ];
    }

    protected static function computed_fields()
    {
        return parent::computed_fields() + [
            'secteur' => [null, 'secteur', PDO::PARAM_STR],
        ];
    }

    function __construct(
        ?int $id,
        string $email,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        Adresse $adresse,
        readonly string $denomination,
        protected ?string $secteur = null,
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
