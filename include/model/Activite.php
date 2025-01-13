<?php
require_once 'model/Offre.php';

/**
 * @inheritDoc
 */
final class Activite extends Offre
{
    protected static function fields()
    {
        return parent::fields() + [
            'indication_duree'         => [null, 'indication_duree',         PDO::PARAM_STR],
            'age_requis'               => [null, 'age_requis',               PDO::PARAM_INT],
            'prestations_incluses'     => [null, 'prestations_incluses',     PDO::PARAM_STR],
            'prestations_non_incluses' => [null, 'prestations_non_incluses', PDO::PARAM_STR],
        ];
    }

    function __construct(
        array $args_offre,
        //
        public Duree $indication_duree,
        public ?int $age_requis,
        public string $prestations_incluses,
        public ?string $prestations_non_incluses,
    ) {
        parent::__construct(...$args_offre);
    }

    const CATEGORIE = 'activité';
    const TABLE     = 'activite';
}
