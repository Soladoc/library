<?php

require_once 'db.php';
require_once 'model/Model.php';
require_once 'model/Image.php';

/**
 * Un livre
 */
class Livre extends Model
{
    protected static function fields()
    {
        return [
            'titre'    => [null, 'titre', PDO::PARAM_STR],
            'resume'   => [null, 'resume', PDO::PARAM_STR],
            'id_image' => [fn($x) => $x?->id, 'image', PDO::PARAM_INT],
        ];
    }

    public ?Image $image;

    function __construct(
        protected ?int $id,
        public string $titre,
        public ?string $resume,
        ?Image $image = null,
    ) {
        $this->image = $image;
    }

    function push_to_db(): void
    {
        if ($this->image) $this->image->push_to_db();
        parent::push_to_db();
    }

    static function from_db(int $id_livre): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' WHERE ' . static::TABLE . '.id = ?'));
        DB\bind_values($stmt, [1 => [$id_livre, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }

    static function from_db_by_search(string $search): Iterator
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' WHERE ' . static::TABLE . '.titre ILIKE ?'));
        notfalse($stmt->execute(["%$search%"]));
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => self::from_db_row($row);
        }
    }

    private static function make_select(): string
    {
        return 'SELECT
            id,
            titre,
            resume,
            id_image
        FROM ' . static::TABLE;
    }

    protected static function from_db_row(array $row): self
    {
        return new self(
            $row['id'],
            $row['titre'],
            $row['resume'] ?? null,
            $row['id_image'] ? Image::from_db($row['id_image']) : null
        );
    }

    const TABLE = '_livre';
}