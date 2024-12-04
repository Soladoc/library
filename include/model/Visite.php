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
        readonly Duree $indication_duree,
        //
        ?FiniteTimestamp $modifiee_le                     = null,
        ?bool $en_ligne                                   = null,
        ?float $note_moyenne                              = null,
        ?float $prix_min                                  = null,
        ?FiniteTimestamp $creee_le                        = null,
        ?Duree $en_ligne_ce_mois_pendant                  = null,
        ?FiniteTimestamp $changement_ouverture_suivant_le = null,
        ?bool $est_ouverte                                = null,
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
            Duree::parse($row['indication_duree']),
            //
            FiniteTimestamp::parse($row['modifiee_le']),
            //
            $row['en_ligne'],
            notfalse(parse_float($row['note_moyenne'] ?? null)),
            notfalse(parse_float($row['prix_min'] ?? null)),
            FiniteTimestamp::parse($row['creee_le']),
            Duree::parse($row['en_ligne_ce_mois_pendant']),
            FiniteTimestamp::parse($row['changement_ouverture_suivant_le'] ?? null),
            $row['est_ouverte'],
        );
    }

    const CATEGORIE = 'visite';
    const TABLE     = 'visite';
}
