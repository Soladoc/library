<?php

require_once 'db.php';
require_once 'model/MultiRange.php';
require_once 'Equatable.php';

/**
 * @implements Equatable<OuvertureHebdomadaire>
 */
final class OuvertureHebdomadaire implements ArrayAccess, Equatable
{
    const TABLE = '_ouverture_hebdomadaire';

    private Offre $offre;

    /**
     * @var array<int, MultiRange<Time>>
     */
    private array $ouvertures_hebdomadaires = [];

    function __construct(Offre $offre)
    {
        $this->offre = $offre;
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
    public function equals(mixed $other): bool {
        return $other->ouvertures_hebdomadaires === $this->ouvertures_hebdomadaires;
    }
    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $dow): bool {
        return isset($this->ouvertures_hebdomadaires[$dow]);
    }
    
    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $dow): MultiRange {
        return $this->ouvertures_hebdomadaires[$dow];
    }
    
    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $dow, mixed $horaires): void {
        $this->ouvertures_hebdomadaires[$dow] = $horaires;
        notfalse(DB\insert_into(self::TABLE, $this->args($dow) + ['horaires' => $horaires])->execute());
    }
    
    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $dow): void {
        unset($this->ouvertures_hebdomadaires[$dow]);
        notfalse(DB\delete_from(self::TABLE, $this->args($dow))->execute());
    }
}
