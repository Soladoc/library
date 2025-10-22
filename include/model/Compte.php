<?php
require_once 'util.php';

/**
 * Représente un compte utilisateur
 */
class Compte
{
    const TABLE = '_compte';

    protected static function fields()
    {
        return [
            'email'     => [null, 'email', PDO::PARAM_STR],
            'mdp_hash'  => [null, 'mdp_hash', PDO::PARAM_STR],
        ];
    }

    public function __construct(
        public ?int $numero_compte,
        public string $email,
        public string $mdp_hash
    ) {}

    /**
     * Récupère un compte par son numéro
     */
    public static function from_db(int $numero_compte): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(static::make_select() . ' where ' . static::TABLE . '.numero_compte = ?'));
        DB\bind_values($stmt, [1 => [$numero_compte, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : static::from_db_row($row);
    }

    /**
     * Récupère un compte par son email
     */
    public static function from_db_by_email(string $email): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(static::make_select() . ' where ' . static::TABLE . '.email = ?'));
        notfalse($stmt->execute([$email]));
        $row = $stmt->fetch();
        return $row === false ? false : static::from_db_row($row);
    }

    /**
     * Génère la requête SELECT de base
     */
    protected static function make_select(): string
    {
        return 'select
            ' . static::TABLE . '.numero_compte,
            ' . static::TABLE . '.email,
            ' . static::TABLE . '.mdp_hash
            from ' . static::TABLE;
    }

    /**
     * Transforme une ligne SQL en objet Compte
     */
    protected static function from_db_row(array $row): self
    {
        return new self(
            $row['numero_compte'],
            $row['email'],
            $row['mdp_hash']
        );
    }
}