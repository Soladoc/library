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
        ?int $id,
        Adresse $adresse,
        Image $image_principale,
        Professionnel $professionnel,
        Abonnement $abonnement,
        string $titre,
        string $resume,
        string $description_detaillee,
        ?string $url_site_web,
        MultiRange $periodes_ouverture,
        //
        readonly string $carte,
        readonly int $richesse,
        readonly bool $sert_petit_dejeuner,
        readonly bool $sert_brunch,
        readonly bool $sert_dejeuner,
        readonly bool $sert_diner,
        readonly bool $sert_boissons,
        //
        ?FiniteTimestamp $modifiee_le                     = null,
        ?bool $en_ligne                                   = null,
        ?float $note_moyenne                              = null,
        ?float $prix_min                                  = null,
        ?FiniteTimestamp $creee_le                        = null,
        ?Duree $en_ligne_ce_mois_pendant                  = null,
        ?FiniteTimestamp $changement_ouverture_suivant_le = null,
        ?bool $est_ouverte                                = null,
        protected ?int $nb_avis                           = null,
        protected ?string $categorie                      = null,
    ) {
        parent::__construct(
            $id,
            $adresse,
            $image_principale,
            $professionnel,
            $abonnement,
            $titre,
            $resume,
            $description_detaillee,
            $url_site_web,
            $periodes_ouverture,
            $modifiee_le,
            $en_ligne,
            $note_moyenne,
            $prix_min,
            $creee_le,
            $en_ligne_ce_mois_pendant,
            $changement_ouverture_suivant_le,
            $est_ouverte,
        );
    }

    protected static function from_db_row(array $row): self
    {
        return new self(
            $row['id'],
            Adresse::from_db($row['id_adresse']),
            Image::from_db($row['id_image_principale']),
            Professionnel::from_db($row['id_professionnel']),
            Abonnement::from_db($row['libelle_abonnement']),
            $row['titre'],
            $row['resume'],
            $row['description_detaillee'],
            $row['url_site_web'] ?? null,
            MultiRange::parse($row['periodes_ouverture'], FiniteTimestamp::parse(...)),
            //
            $row['carte'],
            $row['richesse'],
            $row['sert_petit_dejeuner'],
            $row['sert_brunch'],
            $row['sert_dejeuner'],
            $row['sert_diner'],
            $row['sert_boissons'],
            //
            FiniteTimestamp::parse($row['modifiee_le']),
            $row['en_ligne'],
            notfalse(parse_float($row['note_moyenne'] ?? null)),
            notfalse(parse_float($row['prix_min'] ?? null)),
            FiniteTimestamp::parse($row['creee_le']),
            Duree::parse($row['en_ligne_ce_mois_pendant']),
            FiniteTimestamp::parse($row['changement_ouverture_suivant_le'] ?? null),
            $row['est_ouverte'], $row['nb_avis'], $row['categorie']
        );
    }

    const CATEGORIE = 'restaurant';
    const TABLE     = 'restaurant';
}
