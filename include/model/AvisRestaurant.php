<?php

final class AvisRestaurant extends Avis
{
    protected static function fields()
    {
        return parent::fields() + [
            'note_cuisine'      => [null, 'note_cuisine',      PDO::PARAM_INT],
            'note_service'      => [null, 'note_service',      PDO::PARAM_INT],
            'note_ambiance'     => [null, 'note_ambiance',     PDO::PARAM_INT],
            'note_qualite_prix' => [null, 'note_qualite_prix', PDO::PARAM_INT],
        ];
    }

    function __construct(
        array $args_avis,
        readonly int $note_cuisine,
        readonly int $note_service,
        readonly int $note_ambiance,
        readonly int $note_qualite_prix,
    ) {
        parent::__construct(...$args_avis);
    }

    const TABLE = 'avis_restaurant';
}
