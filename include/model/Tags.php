<?php
require_once 'db.php';
require_once 'Equatable.php';

/**
 * @implements Equatable<Tags>
 * @implements IteratorAggregate<string, true>
 */
final class Tags implements IteratorAggregate, Equatable
{
    /** @var array<string, true> */
    private array $tags = [];

    function __construct(
        private readonly Offre $offre
    ) {}

    function add(string $tag): void
    {
        $this->tags[$tag] = true;
        $this->insert_tag($tag);
    }

    function remove(string $tag): void
    {
        unset($this->tags[$tag]);
        if (null !== $args = $this->args($tag)) {
            notfalse(DB\delete_from(self::TABLE, $args)->execute());
        }
    }

    function insert(): void
    {
        foreach (array_keys($this->tags) as $tag) {
            $this->insert_tag($tag);
        }
    }

    private function insert_tag(string $tag): void
    {
        if (null !== $args = $this->args($tag)) {
            notfalse(DB\insert_into(self::TABLE, $args)->execute());
        }
    }

    /**
     * @inheritDoc
     */
    function getIterator(): Traversable
    {
        return new ArrayIterator(array_keys($this->tags));
    }

    private function args(string $tag): ?array
    {
        return $this->offre->id === null ? null : [
            'id_offre' => [$this->offre->id, PDO::PARAM_INT],
            'tag'      => [$tag, PDO::PARAM_STR],
        ];
    }

    /**
     * @inheritDoc
     */
    function equals(mixed $other): bool
    {
        return $other->tags === $this->tags;
    }

    const TABLE = '_tags';
}
