<?php
require_once 'db.php';
require_once 'Equatable.php';

/**
 * @implements Equatable<Galerie>
 */
final class Galerie implements IteratorAggregate, Equatable {
    const TABLE = '_galerie';

    private readonly Offre $offre;

    /**
     * @var array<int, Image>
     */
    private array $images = [];

    function __construct(Offre $offre) {
        $this->offre = $offre;
    }

    function add(Image $image) {
        $this->images[$image->id] = $image;
        notfalse(DB\insert_into(self::TABLE, $this->args($image->id)));
    }

    function remove(Image $image) {
        unset($this->images[$image->id]);
        notfalse(DB\delete_from(self::TABLE, $this->args($image->id)));
    }

    /**
     * @inheritDoc
     */
    function getIterator(): Traversable {
        return new ArrayIterator($this->images);
    }

    private function args(int $id_image): array
    {
        return [
            'id_offre' => [$this->offre->id, PDO::PARAM_INT],
            'id_image' => [$id_image, PDO::PARAM_INT],
        ];
    }
    /**
     * @inheritDoc
     */
    public function equals(mixed $other): bool {
        return $other->images === $this->images;
    }
}