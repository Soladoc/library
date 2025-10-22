<?php
require_once 'db.php';
require_once 'Equatable.php';

/**
 * @implements Equatable<Genres>
 * @implements IteratorAggregate<string, true>
 */
final class Genres implements IteratorAggregate, Equatable
{
    /** @var array<string, true> */
    private array $genres = [];

    function __construct(
        private readonly Livre $livre
    ) {}

    function add(string $genre): void
    {
        $this->genres[$genre] = true;
        $this->insert_genre($genre);
    }

    function remove(string $genre): void
    {
        unset($this->genres[$genre]);
        if (null !== $args = $this->args($genre)) {
            notfalse(DB\delete_from(self::TABLE, $args)->execute());
        }
    }

    function push_to_db(): void
    {
        foreach (array_keys($this->genres) as $genre) {
            $this->insert_genre($genre);
        }
    }

    private function insert_genre(string $genre): void
    {
        if (null !== $args = $this->args($genre)) {
            notfalse(DB\insert_into(self::TABLE, $args)->execute());
        }
    }

    function getIterator(): Traversable
    {
        return new ArrayIterator(array_keys($this->genres));
    }

    private function args(string $genre): ?array
    {
        return $this->livre->id === null ? null : [
            'id_livre' => [$this->livre->id, PDO::PARAM_INT],
            'genre'    => [$genre, PDO::PARAM_STR],
        ];
    }

    function equals(mixed $other): bool
    {
        return $other->genres === $this->genres;
    }

    const TABLE = '_genres';
}