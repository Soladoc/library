<?php

require_once 'db.php';

final class Tarifs implements IteratorAggregate
{
    const TABLE = '_tarif';

    private Offre $offre;

    /**
     * @var array<string, float>
     */
    private array $horaires;

    function __construct(Offre $offre)
    {
        $this->offre = $offre;
    }

    function add(string $nom, float $montant): void
    {
        $this->horaires[$nom] = $montant;
        notfalse(DB\insert_into(self::TABLE, $this->args($nom) + ['montant' => $montant])->execute());
    }

    function remove(string $nom)
    {
        unset($this->horaires[$nom]);
        notfalse(DB\delete_from(self::TABLE, $this->args($nom))->execute());
    }

    private function args(string $nom): array
    {
        return [
            'id_offre' => $this->offre->id,
            'nom' => $nom,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable {
        return new ArrayIterator($this->horaires);
    }
}
