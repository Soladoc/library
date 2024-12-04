<?php
require_once 'model/Offre.php';

/**
 * @inheritDoc
 */
final class Restaurant extends Offre
{
    protected static function fields()
    {
        return parent::fields() + [
            'carte'               => [null, 'carte',               PDO::PARAM_STR],
            'richesse'            => [null, 'richesse',            PDO::PARAM_INT],
            'sert_petit_dejeuner' => [null, 'sert_petit_dejeuner', PDO::PARAM_BOOL],
            'sert_brunch'         => [null, 'sert_brunch',         PDO::PARAM_BOOL],
            'sert_dejeuner'       => [null, 'sert_dejeuner',       PDO::PARAM_BOOL],
            'sert_diner'          => [null, 'sert_diner',          PDO::PARAM_BOOL],
            'sert_boissons'       => [null, 'sert_boissons',       PDO::PARAM_BOOL],
        ];
    }

    // todo: langues

    function __construct(
        array $args,
        readonly string $carte,
        readonly int $richesse,
        readonly bool $sert_petit_dejeuner,
        readonly bool $sert_brunch,
        readonly bool $sert_dejeuner,
        readonly bool $sert_diner,
        readonly bool $sert_boissons,
    ) {
        parent::__construct(...$args);
    }

    const CATEGORIE = 'restaurant';
    const TABLE     = 'restaurant';
}
