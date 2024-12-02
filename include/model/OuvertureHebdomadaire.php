<?php

require_once 'db.php';
require_once 'model/MultiRange.php';

final class OuvertureHebdomadaire implements IteratorAggregate
{
    const TABLE = '_ouverture_hebdomadaire';

    private Offre $offre;

    /**
     * @var array<int, MultiRange<Time>>
     */
    private array $ouvertures_hebdomadaires;

    function __construct(Offre $offre)
    {
        $this->offre = $offre;
    }

    /**
     * @param int $dow
     * @param MultiRange<Time> $horaires
     * @return void
     */
    function add(int $dow, MultiRange $horaires): void
    {
        $this->ouvertures_hebdomadaires[$dow] = $horaires;
        notfalse(DB\insert_into(self::TABLE, $this->args($dow) + ['horaires' => $horaires])->execute());
    }

    function remove(string $nom)
    {
        unset($this->ouvertures_hebdomadaires[$nom]);
        notfalse(DB\delete_from(self::TABLE, $this->args($nom))->execute());
    }

    private function args(int $dow): array
    {
        return [
            'id_offre' => $this->offre->id,
            'dow' => $dow,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable {
        return new ArrayIterator($this->ouvertures_hebdomadaires);
    }
}
