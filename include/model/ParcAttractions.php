<?php
require_once 'model/Offre.php';

/**
 * @inheritDoc
 */
final class ParcAttractions extends Offre
{
    protected static function fields()
    {
        return parent::fields() + [
            'age_requis'     => [null, 'age_requis',     PDO::PARAM_INT],
            'nb_attractions' => [null, 'nb_attractions', PDO::PARAM_INT],
            'id_image_plan'  => [fn($x) => $x->id, 'image_plan', PDO::PARAM_INT],
        ];
    }

    function __construct(
        array $args_offre,
        public ?int $age_requis,
        public int $nb_attractions,
        public Image $image_plan,
    ) {
        parent::__construct(...$args_offre);
    }

    function push_to_db(): void
    {
        $this->image_plan->push_to_db();
        parent::push_to_db();
    }

    const CATEGORIE = "parc d'attractions";
    const TABLE     = 'parc_attractions';
}
