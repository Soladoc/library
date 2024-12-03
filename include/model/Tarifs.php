<?php

require_once 'db.php';
require_once 'Equatable.php';

/**
 * @implements Equatable<Tarifs>
 * @implements IteratorAggregate<string, float>
 */
final class Tarifs implements IteratorAggregate, Equatable
{
    const TABLE = '_tarif';

    private Offre $offre;

    /**
     * @var array<string, float>
     */
    private array $tarifs = [];

    function __construct(Offre $offre)
    {
        $this->offre = $offre;
    }

    function add(string $nom, float $montant): void
    {
        $this->tarifs[$nom] = $montant;
        $this->insert_tarif($nom, $montant);
    }

    function remove(string $nom): void
    {
        unset($this->tarifs[$nom]);
        if (null !== $args = $this->args($nom)) {
            notfalse(DB\delete_from(self::TABLE, $args)->execute());
        }
    }

    private function args(string $nom): ?array
    {
        return $this->offre->id === null ? null : [
            'id_offre' => [$this->offre->id, PDO::PARAM_INT],
            'nom'      => [$nom, PDO::PARAM_STR],
        ];
    }

    function insert(): void
    {
        array_walk($this->tarifs, $this->insert_tarif(...));
    }

    private function insert_tarif(string $nom, float $montant): void
    {
        if (null !== $args = $this->args($nom)) {
            notfalse(DB\insert_into(self::TABLE, $args + [
                'montant' => [$montant, PDO_PARAM_FLOAT],
            ])->execute());
        }
    }

    /**
     * @inheritDoc
     */
    function getIterator(): Traversable
    {
        return new ArrayIterator($this->tarifs);
    }

    /**
     * @inheritDoc
     */
    function equals(mixed $other): bool
    {
        return $other->tarifs === $this->tarifs;
    }
}
