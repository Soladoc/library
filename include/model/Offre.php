<?php

require_once 'db.php';
require_once 'model/Abonnement.php';
require_once 'model/Adresse.php';
require_once 'model/Avis.php';
require_once 'model/Duree.php';
require_once 'model/FiniteTimestamp.php';
require_once 'model/Galerie.php';
require_once 'model/Image.php';
require_once 'model/Model.php';
require_once 'model/MultiRange.php';
require_once 'model/OuvertureHebdomadaire.php';
require_once 'model/Professionnel.php';
require_once 'model/Signalable.php';
require_once 'model/Tags.php';
require_once 'model/Tarifs.php';

/**
 * Une offre touristique.
 * @property-read ?int $id L'ID. `null` si cette offre n'existe pas dans la BDD.
 *
 * @property-read ?bool $en_ligne Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?float $note_moyenne Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?float $prix_min Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?bool $est_ouverte Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?Duree $en_ligne_ce_mois_pendant Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?FiniteTimestamp $creee_le Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?FiniteTimestamp $modifiee_le Calculé mais avec un possibilité de valeur initiale.
 * @property-read ?FiniteTimestamp $changement_ouverture_suivant_le Calculé. `null` si cette offre n'existe pas dans la BDD.
 * @property-read ?string $categorie Calculé. `null` si cette offre n'existe pas dans la BDD.
 *
 * @property-read ?int $nb_avis Le nombre d'avis ce cette offre. Calculé. `null` si cette offre n'existe pas dans la BDD.
 */
abstract class Offre extends Model implements Signalable
{
    protected static function key_fields()
    {
        return [
            'id' => [null, 'id', PDO::PARAM_INT],
        ];
    }

    protected static function computed_fields()
    {
        return [
            'nb_avis'                         => [null, 'nb_avis',  PDO::PARAM_INT],
            'en_ligne'                        => [null, 'en_ligne', PDO::PARAM_BOOL],
            'note_moyenne'                    => [null, 'note_moyenne', PDO_PARAM_FLOAT],
            'prix_min'                        => [null, 'prix_min',     PDO_PARAM_FLOAT],
            'est_ouverte'                     => [null, 'est_ouverte', PDO::PARAM_BOOL],
            'categorie'                       => [null, 'categorie',   PDO::PARAM_STR],
            'en_ligne_ce_mois_pendant'        => [Duree::parse(...),           'en_ligne_ce_mois_pendant',        PDO::PARAM_STR],
            'creee_le'                        => [FiniteTimestamp::parse(...), 'creee_le',                        PDO::PARAM_STR],
            'modifiee_le'                     => [FiniteTimestamp::parse(...), 'modifiee_le',                     PDO::PARAM_STR],
            'changement_ouverture_suivant_le' => [FiniteTimestamp::parse(...), 'changement_ouverture_suivant_le', PDO::PARAM_STR],
        ];
    }

    protected static function fields()
    {
        return [
            'titre'                 => [null, 'titre',                 PDO::PARAM_STR],
            'resume'                => [null, 'resume',                PDO::PARAM_STR],
            'description_detaillee' => [null, 'description_detaillee', PDO::PARAM_STR],
            'modifiee_le'           => [null, 'modifiee_le',           PDO::PARAM_STR],
            'url_site_web'          => [null, 'url_site_web',          PDO::PARAM_STR],
            'periodes_ouverture'    => [null, 'periodes_ouverture',    PDO::PARAM_STR],
            'id_adresse'            => [fn($x) => $x->id,      'adresse',          PDO::PARAM_INT],
            'id_image_principale'   => [fn($x) => $x->id,      'image_principale', PDO::PARAM_INT],
            'id_professionnel'      => [fn($x) => $x->id,      'professionnel',    PDO::PARAM_INT],
            'libelle_abonnement'    => [fn($x) => $x->libelle, 'abonnement',       PDO::PARAM_STR],
        ];
    }

    readonly Tags $tags;
    readonly Tarifs $tarifs;
    readonly OuvertureHebdomadaire $ouverture_hebdomadaire;
    readonly Galerie $galerie;

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
     * @param MultiRange<FiniteTimestamp> $periodes_ouverture
     * @param ?FiniteTimestamp $modifiee_le
     * @param ?bool $en_ligne
     * @param ?float $note_moyenne
     * @param ?float $prix_min
     * @param ?FiniteTimestamp $creee_le
     * @param ?Duree $en_ligne_ce_mois_pendant
     * @param ?FiniteTimestamp $changement_ouverture_suivant_le
     * @param ?bool $est_ouverte
     */
    function __construct(
        protected ?int $id,
        readonly Adresse $adresse,
        readonly Image $image_principale,
        readonly Professionnel $professionnel,
        readonly Abonnement $abonnement,
        readonly string $titre,
        readonly string $resume,
        readonly string $description_detaillee,
        readonly ?string $url_site_web,
        readonly MultiRange $periodes_ouverture,
        protected ?FiniteTimestamp $modifiee_le                     = null,
        protected ?bool $en_ligne                                   = null,
        protected ?float $note_moyenne                              = null,
        protected ?float $prix_min                                  = null,
        protected ?FiniteTimestamp $creee_le                        = null,
        protected ?Duree $en_ligne_ce_mois_pendant                  = null,
        protected ?FiniteTimestamp $changement_ouverture_suivant_le = null,
        protected ?bool $est_ouverte                                = null,
        protected ?int $nb_avis                                     = null,
        protected ?string $categorie                                = null,
    ) {
        $this->tags                   = new Tags($this);
        $this->tarifs                 = new Tarifs($this);
        $this->ouverture_hebdomadaire = new OuvertureHebdomadaire($this);
        $this->galerie                = new Galerie($this);
    }

    function push_to_db(): void
    {
        $this->professionnel->push_to_db();
        $this->adresse->push_to_db();
        $this->image_principale->push_to_db();
        parent::push_to_db();
        $this->tarifs->insert();
        $this->tags->insert();
        $this->ouverture_hebdomadaire->push_to_db();
        $this->galerie->push_to_db();
    }

    function alterner_etat()
    {
        if ($this->id === null) {
            throw new LogicException('cette offre doit exister dans la bdd');
        }
        $stmt = notfalse(DB\connect()->prepare('insert into _changement_etat (id_offre) values (?)'));
        DB\bind_values($stmt, [1 => [$this->id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
    }

    static function from_db(int $id_offre): Offre|false
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_offre, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        if ($row === false) return false;
        return self::from_db_row($row);
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Iterator<int, Offre> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_a_la_une(): Iterator
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . " where libelle_abonnement = 'premium' AND en_ligne = TRUE order by random() limit 3"));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => self::from_db_row($row);
        }
    }

    /**
     * Récupère des offres de la BDD.
     * @param mixed $id_professionnel L'ID du professionnel dont on veut récupérer les offres, ou `null` pour récupérer les offres de tous les professionnels.
     * @param mixed $en_ligne Si on veut les offres actuellement en ligne ou hors ligne, ou `null` pour les deux.
     * @return Iterator<int, Offre> Les offres de la BDD répondant au critères passés en paramètre.
     */
    static function from_db_all(?int $id_professionnel = null, ?bool $en_ligne = null): Iterator
    {
        $args = DB\filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . DB\where_clause(DB\BoolOperator::AND, array_keys($args))));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => self::from_db_row($row);
        }
    }

    /**
     * Récupère les offres de la BDD dont le titre correspond à une recherche.
     * @param string $motcle La chaîne recherchée
     * @return Iterator<int, Offre>
     */
    static function from_db_by_motcle(string $motcle): Iterator
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where '
            . implode(' and ', array_map(
                fn($mot) => 'titre ilike ' . DB\quote_string("%$mot%"),
                explode(' ', trim($motcle)),
            ))));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => self::from_db_row($row);
        }
    }

    private static function make_select(): string
    {
        // Todo: this is just a proof of concept for single-query inheritance.
        return 'select
            id,id_adresse,id_image_principale,id_professionnel,libelle_abonnement,titre,resume,description_detaillee,modifiee_le,url_site_web,periodes_ouverture,en_ligne,note_moyenne,prix_min,nb_avis,creee_le,categorie,en_ligne_ce_mois_pendant,changement_ouverture_suivant_le,est_ouverte,
            
            _activite.indication_duree activite_indication_duree,
            _activite.age_requis activite_age_requis,
            _activite.prestations_incluses activite_prestations_incluses,
            _activite.prestations_non_incluses activite_prestations_non_incluses,

            _parc_attractions.id_image_plan parc_attractions_id_image_plan,
            _parc_attractions.nb_attractions parc_attractions_nb_attractions,
            _parc_attractions.age_requis parc_attractions_age_requis,

            _restaurant.carte restaurant_carte,
            _restaurant.richesse restaurant_richesse,
            _restaurant.sert_petit_dejeuner restaurant_sert_petit_dejeuner,
            _restaurant.sert_brunch restaurant_sert_brunch,
            _restaurant.sert_dejeuner restaurant_sert_dejeuner,
            _restaurant.sert_diner restaurant_sert_diner,
            _restaurant.sert_boissons restaurant_sert_boissons,

            _spectacle.indication_duree spectacle_indication_duree,
            _spectacle.capacite_accueil spectacle_capacite_accueil,
            
            _visite.indication_duree visite_indication_duree

            from ' . self::TABLE . '
                left join _activite using (id)
                left join _parc_attractions using (id)
                left join _restaurant using (id)
                left join _spectacle using (id)
                left join _visite using (id)';
    }

    protected static function from_db_row(array $row): Offre
    {
        self::require_subclasses();
        $args = [
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
            FiniteTimestamp::parse($row['modifiee_le']),
            $row['en_ligne'],
            notfalse(parse_float($row['note_moyenne'] ?? null)),
            notfalse(parse_float($row['prix_min'] ?? null)),
            FiniteTimestamp::parse($row['creee_le']),
            Duree::parse($row['en_ligne_ce_mois_pendant']),
            FiniteTimestamp::parse($row['changement_ouverture_suivant_le'] ?? null),
            $row['est_ouverte'],
            $row['nb_avis'],
            $row['categorie'],
        ];
        return match ($row['categorie']) {
            Activite::CATEGORIE => new Activite(
                $args,
                Duree::parse($row['activite_indication_duree']),
                $row['activite_age_requis'] ?? null,
                $row['activite_prestations_incluses'],
                $row['activite_prestations_non_incluses'] ?? null,
            ),
            ParcAttractions::CATEGORIE => new ParcAttractions(
                $args,
                $row['parc_attractions_age_requis'],
                $row['parc_attractions_nb_attractions'],
                Image::from_db($row['parc_attractions_id_image_plan']),
            ),
            Restaurant::CATEGORIE => new Restaurant(
                $args,
                $row['restaurant_carte'],
                $row['restaurant_richesse'],
                $row['restaurant_sert_petit_dejeuner'],
                $row['restaurant_sert_brunch'],
                $row['restaurant_sert_dejeuner'],
                $row['restaurant_sert_diner'],
                $row['restaurant_sert_boissons'],
            ),
            Spectacle::CATEGORIE => new Spectacle(
                $args,
                Duree::parse($row['spectacle_indication_duree']),
                $row['spectacle_capacite_accueil'],
            ),
            Visite::CATEGORIE => new Visite(
                $args,
                Duree::parse($row['visite_indication_duree']),
            ),
        };
    }

    private static function require_subclasses(): void
    {
        require_once 'model/Activite.php';
        require_once 'model/ParcAttractions.php';
        require_once 'model/Restaurant.php';
        require_once 'model/Spectacle.php';
        require_once 'model/Visite.php';
    }

    const TABLE = 'offres';

    /**
     * @var string
     */
    const CATEGORIE = null;  // abstract constant
}
