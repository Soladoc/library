<?php
require_once 'db.php';

final class Tags implements IteratorAggregate
{
    const TABLE = '_tags';

    /**
     * @var array<string, true>
     */
    private array $tags = [];
    private readonly Offre $offre;

    function __construct(Offre $offre)
    {
        $this->offre = $offre;
    }

    function add(string $tag): void
    {
        $this->tags[$tag] = true;
        notfalse(DB\insert_into(self::TABLE, $this->args($tag))->execute());
    }

    function remove(string $tag): void
    {
        unset($this->tags[$tag]);
        notfalse(DB\delete_from(self::TABLE, $this->args($tag))->execute());
    }

    /**
     * @inheritDoc
     */
    function getIterator(): Traversable
    {
        return new ArrayIterator(array_keys($this->tags));
    }

    private function args(string $tag): array
    {
        return [
            'id_offre' => [$this->offre->id, PDO::PARAM_INT],
            'tag' => [$tag, PDO::PARAM_STR],
        ];
    }
}
