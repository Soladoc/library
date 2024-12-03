<?php
require_once 'model/Offre.php';

/**
 * @inheritDoc
 * @property string $carte
 * @property int $richesse
 * @property bool $sert_petit_dejeuner
 * @property bool $sert_brunch
 * @property bool $sert_dejeuner
 * @property bool $sert_diner
 * @property bool $sert_boissons
 */
final class Restaurant extends Offre
{
    protected const FIELDS = parent::FIELDS + [
        'carte'               => [[null, 'carte',               PDO::PARAM_STR]],
        'richesse'            => [[null, 'richesse',            PDO::PARAM_INT]],
        'sert_petit_dejeuner' => [[null, 'sert_petit_dejeuner', PDO::PARAM_BOOL]],
        'sert_brunch'         => [[null, 'sert_brunch',         PDO::PARAM_BOOL]],
        'sert_dejeuner'       => [[null, 'sert_dejeuner',       PDO::PARAM_BOOL]],
        'sert_diner'          => [[null, 'sert_diner',          PDO::PARAM_BOOL]],
        'sert_boissons'       => [[null, 'sert_boissons',       PDO::PARAM_BOOL]],
    ];

    const CATEGORIE = 'restaurant';
    const TABLE     = 'restaurant';

    protected string $carte;
    protected int $richesse;
    protected bool $sert_petit_dejeuner;
    protected bool $sert_brunch;
    protected bool $sert_dejeuner;
    protected bool $sert_diner;
    protected bool $sert_boissons;

    // todo: langues

    /**
     * Construit une nouvelle activité.
     * @param ?int $id
     * @param Adresse $adresse
     * @param Image $image_principale
     * @param Professionnel $professionnel
     * @param Abonnement $abonnement
     * @param string $titre
     * @param string $resume
     * @param string $description_detaillee
     * @param ?string $url_site_web
     * @param MultiRange<FiniteTimestamp> $periodes_ouverture
     * @param ?FiniteTimestamp $modifiee_le
     * @param ?bool $en_ligne
     * @param ?float $note_moyenne
     * @param ?float $prix_min
     * @param ?FiniteTimestamp $creee_le
     * @param ?Duree $en_ligne_ce_mois_pendant
     * @param ?FiniteTimestamp $changement_ouverture_suivant_le
     * @param ?bool $est_ouverte
     * @param string $carte
     * @param int $richesse
     * @param bool $sert_petit_dejeuner
     * @param bool $sert_brunch
     * @param bool $sert_dejeuner
     * @param bool $sert_diner
     * @param bool $sert_boissons
     */
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
        string $carte,
        int $richesse,
        bool $sert_petit_dejeuner,
        bool $sert_brunch,
        bool $sert_dejeuner,
        bool $sert_diner,
        bool $sert_boissons,
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
        $this->carte               = $carte;
        $this->richesse            = $richesse;
        $this->sert_petit_dejeuner = $sert_petit_dejeuner;
        $this->sert_brunch         = $sert_brunch;
        $this->sert_dejeuner       = $sert_dejeuner;
        $this->sert_diner          = $sert_diner;
        $this->sert_boissons       = $sert_boissons;
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
            $row['est_ouverte'],
        );
    }
}
