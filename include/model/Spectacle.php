<?php
require_once 'model/Offre.php';

/**
 * @inheritDoc
 */
final class Spectacle extends Offre
{
    protected static function fields()
    {
        return parent::fields() + [
            'indication_duree' => [null, 'indication_duree', PDO::PARAM_STR],
            'capacite_accueil' => [null, 'capacite_accueil', PDO::PARAM_INT],
        ];
    }

    function __construct(
        array $args,
        readonly Duree $indication_duree,
        readonly int $capacite_accueil,
    ) {
        parent::__construct(...$args);
    }

    const CATEGORIE = 'spectacle';
    const TABLE     = 'spectacle';
}
