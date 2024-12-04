<?php

/**
 * @inheritDoc
 */
final class Membre extends Compte
{
    protected static function fields()
    {
        return parent::fields() + [
            'pseudo' => [null, 'pseudo', PDO::PARAM_STR],
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
        readonly string $pseudo,
    ) {
        parent::__construct($id,
            $email,
            $mdp_hash,
            $nom,
            $prenom,
            $telephone,
            $adresse);
    }

    static function from_db(int $id_membre): Membre|false {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_membre, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        if ($row === false) return false;
        return static::from_db_row($row);
    }

    protected static function from_db_row(array $row): Membre {
        new Membre(
            $row['id'],
            $row['email'],
            $row['mdp_hash'],
            $row['nom'],
            $row['prenom'],
            $row['telephone'],
            Adresse::from_db($row['id_adresse']),
            $row['pseudo'],
        );
    }

    private static function make_select(): string
    {
        return 'select * from ' . static::TABLE;  // todo: faire des jointures pour gagner en performance
    }

    const TABLE = 'membre';
}
