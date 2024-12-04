<?php
require_once 'model/Offre.php';

/**
 * @inheritDoc
 */
final class Visite extends Offre
{
    protected static function fields()
    {
        return parent::fields() + [
            'indication_duree' => [null, 'indication_duree', PDO::PARAM_STR],
        ];
    }

    // todo: langues

    function __construct(
        array $args,
        readonly Duree $indication_duree,
    ) {
        parent::__construct(...$args);
    }

    const CATEGORIE = 'visite';
    const TABLE     = 'visite';
}
