<?php

require_once 'db.php';
require_once 'Equatable.php';

/**
 * @implements Equatable<Tarifs>
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
        notfalse(DB\insert_into(self::TABLE, $this->args($nom) + [
            'montant' => [$montant, PDO_PARAM_FLOAT],
        ])->execute());
    }

    function remove(string $nom)
    {
        unset($this->tarifs[$nom]);
        notfalse(DB\delete_from(self::TABLE, $this->args($nom))->execute());
    }

    private function args(string $nom): array
    {
        return [
            'id_offre' => [$this->offre->id, PDO::PARAM_INT],
            'nom' => [$nom, PDO::PARAM_STR],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable {
        return new ArrayIterator($this->tarifs);
    }
    /**
     * @inheritDoc
     */
    public function equals(mixed $other): bool {
        return $other->tarifs === $this->tarifs;
    }
}
