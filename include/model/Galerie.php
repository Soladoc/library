<?php
require_once 'db.php';
require_once 'Equatable.php';

/**
 * @implements Equatable<Galerie>
 * @implements IteratorAggregate<int, Image>
 */
final class Galerie implements IteratorAggregate, Equatable {
    const TABLE = '_galerie';

    private readonly Offre $offre;

    /**
     * @var Image[]
     */
    private array $images = [];

    function __construct(Offre $offre) {
        $this->offre = $offre;
    }

    function add(Image $image): void {
        $this->images[] = $image;
        $this->insert_galerie($image);
    }

    function remove(Image $image): void {
        $this->images = array_diff($this->images, [$image]);
        if (null !== $args = $this->args($image->id)) {
            notfalse(DB\delete_from(self::TABLE, $args));
        }
    }

    function insert(): void {
        foreach ($this->images as $image) {
            $this->insert_galerie($image);
        }
    }

    /**
     * @inheritDoc
     */
    function getIterator(): Traversable {
        return new ArrayIterator($this->images);
    }

    private function args(int $id_image): ?array
    {
        return $this->offre->id === null ? null : [
            'id_offre' => [$this->offre->id, PDO::PARAM_INT],
            'id_image' => [$id_image, PDO::PARAM_INT],
        ];
    }
    /**
     * @inheritDoc
     */
    function equals(mixed $other): bool {
        return $other->images === $this->images;
    }

    private function insert_galerie(Image $image): void {
        if (null !== $args = $this->args($image->id)) {
            $image->insert();
            notfalse(DB\insert_into(self::TABLE, $args));
        }
    }
}