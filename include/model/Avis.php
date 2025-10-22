<?php

require_once 'db.php';
require_once 'model/Livre.php';

/**
 * @property-read ?int $id L'ID. `null` si cet avis n'existe pas dans la BDD.
 */
class Avis extends Model
{
    protected static function key_fields()
    {
        return [
            'id' => [null, 'id', PDO::PARAM_INT],
        ];
    }

    protected static function fields()
{
    return [
        'titre_avis'        => [null, 'titre_avis', PDO::PARAM_STR],
        'commentaire'       => [null, 'commentaire', PDO::PARAM_STR],
        'note'              => [null, 'note', PDO::PARAM_INT],
        'note_ecriture'     => [null, 'note_ecriture', PDO::PARAM_INT],
        'note_intrigue'     => [null, 'note_intrigue', PDO::PARAM_INT],
        'note_personnages'  => [null, 'note_personnages', PDO::PARAM_INT],
        'id_livre'          => [fn($x) => $x?->id, 'livre', PDO::PARAM_INT],
    ];
}

    class Avis
{
    public function __construct(
            public int $id,
            public ?string $titre_avis,
            public ?string $commentaire,
            public int $note,
            public ?int $note_ecriture,
            public ?int $note_intrigue,
            public ?int $note_personnages,
            public Livre $livre
        ) {}
    }


    static function from_db(int $id_avis): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where ' . static::TABLE . '.id = ?'));
        DB\bind_values($stmt, [1 => [$id_avis, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }

    /**
    * Retourne le seul avis qu'un livre peut avoir, ou `false` s'il n'y en a pas.
    * @param int $id_livre
    * @return Avis|false
    */
    static function from_db_one(int $id_livre): self|false {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' WHERE ' . static::TABLE . '.id_livre = ?'));
        DB\bind_values($stmt, 1 => [$id_livre, PDO::PARAM_INT]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }

    private static function from_db_row(array $row): self {
        return new self(
            $row['id'],
            $row['titre_avis'],
            $row['commentaire'],
            $row['note'],
            $row['note_ecriture'],
            $row['note_intrigue'],
            $row['note_personnages'],
            Livre::from_db($row['id_livre'])
        );
    }

    private static function make_select(): string {
        return 'select
            ' . static::TABLE . '.id,
            ' . static::TABLE . '.titre_avis,
            ' . static::TABLE . '.commentaire,
            ' . static::TABLE . '.note,
            ' . static::TABLE . '.note_ecriture,
            ' . static::TABLE . '.note_intrigue,
            ' . static::TABLE . '.note_personnages,
            ' . static::TABLE . '.id_livre
        from ' . static::TABLE;
    }

    const TABLE = 'avis';
}
