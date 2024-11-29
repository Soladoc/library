<?php
require_once 'db.php';
require_once 'model/Adresse.php';
require_once 'model/Image.php';
require_once 'model/Professionnel.php';
require_once 'model/Signalable.php';

/**
 * Une offre touristique.
 */
abstract class Offre implements Signalable
{
    private const TABLE = 'offres';

    private readonly ?int $id;

    /**
     * L'adresse où se trouve cette offre.
     * @var Adresse
     */
    readonly Adresse $adresse;

    /**
     * L'image principale.
     * @var Image
     */
    readonly Image $image_principale;

    /**
     * Le professionnel proprétaire de cette offre.
     * @var Professionnel
     */
    readonly Professionnel $professionnel;

    /**
     * L'abonnement.
     * @var Abonnement
     */
    readonly Abonnement $abonnement;

    /**
     * Le titre.
     * @var string
     */
    readonly string $titre;

    /**
     * Le résumé.
     * @var string
     */
    readonly string $resume;

    /**
     * La description détaillée.
     * @var string
     */
    readonly string $description_detaillee;

    /**
     * L'URL du site web.
     * @var ?string
     */
    readonly ?string $url_site_web;

    /**
     * Les périodes d'ouverture.
     * @var MultiRange<Timestamp>
     */
    readonly MultiRange $periodes_ouverture;

    /**
     * La date de dernière mise à jour (incluant la création).
     * @var Timestamp
     */
    readonly Timestamp $modifiee_le;

    /**
     * Si cette offre est en ligne.
     * @var bool
     */
    readonly bool $en_ligne;

    /**
     * La note moyenne des avis sur cette offre.
     * @var float
     */
    readonly float $note_moyenne;

    /**
     * Le prix minimal dans la grille tarifaire, ou `null` quand il n'y a pas de grille tarifaire.
     * @var ?float
     */
    readonly ?float $prix_min;

    /**
     * La date de création de cette offre. Est égale à $modifee_le si cette offre n'a jamais été modifée.
     * @var Timestamp
     */
    readonly Timestamp $creee_le;

    /**
     * La durée pendant laquelle cette offre a été en ligne pendant ce mois.
     * @var Duree
     */
    readonly Duree $en_ligne_ce_mois_pendant;

    /**
     * Quand aura lieu le prochain changement d'ouverture (passage de ouvert -> fermé (fermetrue) / fermé -> ouvert (ouverture)).
     * @var Timestamp
     */
    readonly Timestamp $changement_ouverture_suivant_le;

    /**
     * Si cette offre est actuellement ouverte.
     * @var bool
     */
    readonly bool $est_ouverte;

    /**
     * Construit une nouvelle offre.
     * @param ?int $id
     * @param Adresse $adresse
     * @param Image $image_principale
     * @param Professionnel $professionnel
     * @param Abonnement $abonnement
     * @param string $titre
     * @param string $resume
     * @param string $description_detaillee
     * @param ?string $url_site_web
     * @param MultiRange<Timestamp> $periodes_ouverture
     * @param Timestamp $modifee_le
     * @param bool $en_ligne
     * @param float $note_moyenne
     * @param ?float $prix_min
     * @param Timestamp $creee_le
     * @param Duree $en_ligne_ce_mois_pendant
     * @param Timestamp $changement_ouverture_suivant_le
     * @param bool $est_ouverte
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
        Timestamp $modifee_le,
        bool $en_ligne,
        float $note_moyenne,
        ?float $prix_min,
        Timestamp $creee_le,
        Duree $en_ligne_ce_mois_pendant,
        Timestamp $changement_ouverture_suivant_le,
        bool $est_ouverte,
    ) {
        $this->id = $id;
        $this->adresse = $adresse;
        $this->image_principale = $image_principale;
        $this->professionnel = $professionnel;
        $this->abonnement = $abonnement;
        $this->titre = $titre;
        $this->resume = $resume;
        $this->description_detaillee = $description_detaillee;
        $this->url_site_web = $url_site_web;
        $this->periodes_ouverture = $periodes_ouverture;
        $this->modifee_le = $modifee_le;
        $this->en_ligne = $en_ligne;
        $this->note_moyenne = $note_moyenne;
        $this->prix_min = $prix_min;
        $this->creee_le = $creee_le;
        $this->en_ligne_ce_mois_pendant = $en_ligne_ce_mois_pendant;
        $this->changement_ouverture_suivant_le = $changement_ouverture_suivant_le;
        $this->est_ouverte = $est_ouverte;
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Offre[] Les offres "À la Une" de la BDD.
     */
    static function from_db_a_la_une(): array
    {
        require_once 'model/ParcAttractions.php';
        require_once 'model/Activite.php';
        require_once 'model/Visite.php';
        require_once 'model/Spectacle.php';
        require_once 'model/Restaurant.php';
        $stmt = notfalse(DB\connect()->prepare('select * from ' . self::TABLE . '
            left join _activite using (id)
            left join _parc_attractions using (id)
            left join _restaurant using (id)
            left join _spectacle using (id)
            left join _visite using (id)
        where note_moyenne = 5'));  // todo: temporaire : le temps qu'on fasse marcher les options
        notfalse($stmt->execute());
        return array_map(self::from_db_row(...), $stmt->fetchAll());
    }

    /**
     * Récupère des offres de la BDD.
     * @param mixed $id_professionnel L'ID du professionnel dont on veut récupérer les offres, ou `null` pour récupérer les offres de tous les professionnels.
     * @param mixed $en_ligne Si on veut les offres actuellement en ligne ou hors ligne, ou `null` pour les deux.
     * @return Offre[] Les offres de la BDD répondant au critères passés en paramètre.
     */
    static function from_db_all(?int $id_professionnel = null, ?bool $en_ligne = null): array
    {
        $args = DB\filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
        $stmt = notfalse(DB\connect()->prepare('select * from ' . self::TABLE . DB\where_clause(DB\BoolOperator::AND, array_keys($args))));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        return array_map(self::from_db_row(...), $stmt->fetchAll());
    }

    static function from_db_by_motcle(string $motcle): array
    {
        $mots = array_map(fn($mot) => 'titre ilike ' . DB\quote_string("%$mot%"), explode(' ', trim($motcle)));
        $stmt = notfalse(DB\connect()->prepare('select * from offres where ' . implode(' and ', $mots)));
        notfalse($stmt->execute());
        return array_map(self::from_db_row(...), $stmt->fetchAll());
    }

    /**
     * @param string[] $row
     * @return Offre
     */
    private static function from_db_row(array $row): Offre
    {
        require_once 'model/ParcAttractions.php';
        require_once 'model/Activite.php';
        require_once 'model/Visite.php';
        require_once 'model/Spectacle.php';
        require_once 'model/Restaurant.php';
        $common_args = [
            getarg($row, 'id', arg_filter(FILTER_VALIDATE_INT)),
            Adresse::from_db(getarg($row, 'id_adresse')),
            Image::from_db(getarg($row, 'id_image_principale')),
            Professionnel::from_db(getarg($row, 'id_professionnel')),
            Abonnement::from_db(getarg($row, 'libelle_abonnement')),
            getarg($row, 'titre'),
            getarg($row, 'resume'),
            getarg($row, 'description_detaillee'),
            getarg($row, 'url_site_web'),
            getarg($row, 'periodes_ouverture'),
            Timestamp::parse(getarg($row, 'modifee_le')),
            getarg($row, 'en_ligne', arg_filter(FILTER_VALIDATE_BOOL)),
            getarg($row, 'note_moyenne', arg_filter(FILTER_VALIDATE_FLOAT)),
            getarg($row, 'prix_min', arg_filter(FILTER_VALIDATE_FLOAT)),
            Timestamp::parse(getarg($row, 'creee_le')),
            Duree::parse(getarg($row, 'en_ligne_ce_mois_pendant')),
            Timestamp::parse(getarg($row, 'changement_ouverture_suivant_le')),
            getarg($row, 'est_ouverte', arg_filter(FILTER_VALIDATE_BOOL)),
        ];
        return match ($row['categorie']) {
            'activité' => new Activite(
                ...$common_args,
                indication_duree: Duree::parse(getarg($row, 'indication_duree')),
                age_requis: getarg($row, 'age_requis', arg_filter(FILTER_VALIDATE_INT)),
                prestations_incluses: getarg($row, 'prestations_incluses'),
                prestations_non_incluses: getarg($row, 'prestations_non_incluses'),
            ),
            "parc d'attractions" => new ParcAttactions(
                ...$common_args,
                id_image_plan: Image::from_db($row['id_image_plan']),
            ),
            'restaurant' => new Restaurant(
                ...$common_args,
                carte: getarg($row, 'carte'),
                richesse: getarg($row, 'richesse', arg_filter(FILTER_VALIDATE_INT)),
                sert_petit_dejeuner: getarg($row, 'sert_petit_dejeuner', arg_filter(FILTER_VALIDATE_BOOL)),
                sert_brunch: getarg($row, 'sert_brunch', arg_filter(FILTER_VALIDATE_BOOL)),
                sert_dejeuner: getarg($row, 'sert_dejeuner', arg_filter(FILTER_VALIDATE_BOOL)),
                sert_diner: getarg($row, 'sert_diner', arg_filter(FILTER_VALIDATE_BOOL)),
                sert_boissons: getarg($row, 'sert_boissons', arg_filter(FILTER_VALIDATE_BOOL)),
            ),
            'spectacle' => new Spectacle(
                ...$common_args,
                indication_duree: Duree::parse(getarg($row, 'indication_duree')),
                capacite_accueil: getarg($row, 'capacite_accueil', arg_filter(FILTER_VALIDATE_INT)),
            ),
            'visite' => new Visite(
                ...$common_args,
                indication_duree: Duree::parse(getarg($row, 'indication_duree')),
            ),
        };
    }
}
