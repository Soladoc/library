<?php
require_once 'db.php';
require_once 'model/Abonnement.php';
require_once 'model/Adresse.php';
require_once 'model/Avis.php';
require_once 'model/Duree.php';
require_once 'model/FiniteTimestamp.php';
require_once 'model/Image.php';
require_once 'model/MultiRange.php';
require_once 'model/Professionnel.php';
require_once 'model/Signalable.php';

/**
 * Une offre touristique.
 * @property-read ?int $id L'ID. `null` si cette image n'existe pas dans la BDD.
 * @property-read int $nb_avis Le nombre d'avis ce cette offre. 0 si elle n'existe pas dans la BDD.
 */
abstract class Offre implements Signalable
{
    function __get(string $name)
    {
        return match ($name) {
            'id' => $this->id,
            'nb_avis' => $this->id === null ? 0 : $this->nb_avis ??= Avis::get_count($this->id),
        };
    }

    const TABLE = 'offres';
    const CATEGORIE = self::CATEGORIE;  // Constante devant être définie dans les sous-classes

    private int $nb_avis;

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
     * @var MultiRange<FiniteTimestamp>
     */
    readonly MultiRange $periodes_ouverture;

    /**
     * La date de dernière mise à jour (incluant la création).
     * @var FiniteTimestamp
     */
    readonly FiniteTimestamp $modifiee_le;

    /**
     * Si cette offre est en ligne.
     * @var bool
     */
    readonly bool $en_ligne;

    /**
     * La note moyenne des avis sur cette offre.
     * @var float
     */
    readonly ?float $note_moyenne;

    /**
     * Le prix minimal dans la grille tarifaire, ou `null` quand il n'y a pas de grille tarifaire.
     * @var ?float
     */
    readonly ?float $prix_min;

    /**
     * La date de création de cette offre. Est égale à `$modifiee_le` si cette offre n'a jamais été modifée.
     * @var FiniteTimestamp
     */
    readonly FiniteTimestamp $creee_le;

    /**
     * La durée pendant laquelle cette offre a été en ligne pendant ce mois.
     * @var Duree
     */
    readonly Duree $en_ligne_ce_mois_pendant;

    /**
     * Quand aura lieu le prochain changement d'ouverture (passage de ouvert -> fermé (fermetrue) / fermé -> ouvert (ouverture)).
     * @var FiniteTimestamp
     */
    readonly ?FiniteTimestamp $changement_ouverture_suivant_le;

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
     * @param MultiRange<FiniteTimestamp> $periodes_ouverture
     * @param FiniteTimestamp $modifiee_le
     * @param bool $en_ligne
     * @param ?float $note_moyenne
     * @param ?float $prix_min
     * @param FiniteTimestamp $creee_le
     * @param Duree $en_ligne_ce_mois_pendant
     * @param ?FiniteTimestamp $changement_ouverture_suivant_le
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
        FiniteTimestamp $modifiee_le,
        bool $en_ligne,
        ?float $note_moyenne,
        ?float $prix_min,
        FiniteTimestamp $creee_le,
        Duree $en_ligne_ce_mois_pendant,
        ?FiniteTimestamp $changement_ouverture_suivant_le,
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
        $this->modifiee_le = $modifiee_le;
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
     * @return Iterator<int, Offre> Les offres "À la Une" de la BDD, indexés par ID.
     */
    static function from_db_a_la_une(): Iterator
    {
        if (static::TABLE === self::TABLE) {
            require_once 'model/ParcAttractions.php';
            require_once 'model/Activite.php';
            require_once 'model/Visite.php';
            require_once 'model/Spectacle.php';
            require_once 'model/Restaurant.php';
            foreach (Activite::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            foreach (ParcAttractions::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            foreach (Restaurant::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            foreach (Spectacle::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            foreach (Visite::from_db_a_la_une() as $id => $row) {
                yield $id => $row;
            }
            return;
        }
        // todo: where temporaire : le temps qu'on fasse marcher les options
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where note_moyenne = 5'));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => static::from_db_row($row);
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
        if (static::TABLE === self::TABLE) {
            require_once 'model/ParcAttractions.php';
            require_once 'model/Activite.php';
            require_once 'model/Visite.php';
            require_once 'model/Spectacle.php';
            require_once 'model/Restaurant.php';
            foreach (Activite::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            foreach (ParcAttractions::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            foreach (Restaurant::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            foreach (Spectacle::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            foreach (Visite::from_db_all($id_professionnel, $en_ligne) as $id => $row) {
                yield $id => $row;
            }
            return;
        }
        $args = DB\filter_null_args(['id_professionnel' => [$id_professionnel, PDO::PARAM_INT], 'en_ligne' => [$en_ligne, PDO::PARAM_BOOL]]);
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . DB\where_clause(DB\BoolOperator::AND, array_keys($args))));
        DB\bind_values($stmt, $args);
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => static::from_db_row($row);
        }
    }

    /**
     * Récupère les offres de la BDD dont le titre correspond à une recherche.
     * @param string $motcle La chaîne recherchée
     * @return Iterator<int, Offre>
     */
    static function from_db_by_motcle(string $motcle): Iterator
    {
        if (static::TABLE === self::TABLE) {
            require_once 'model/ParcAttractions.php';
            require_once 'model/Activite.php';
            require_once 'model/Visite.php';
            require_once 'model/Spectacle.php';
            require_once 'model/Restaurant.php';
            foreach (Activite::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            foreach (ParcAttractions::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            foreach (Restaurant::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            foreach (Spectacle::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            foreach (Visite::from_db_by_motcle($motcle) as $id => $row) {
                yield $id => $row;
            }
            return;
        }
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where '
            . implode(' and ', array_map(
                fn($mot) => 'titre ilike ' . DB\quote_string("%$mot%"),
                explode(' ', trim($motcle)),
            ))));
        notfalse($stmt->execute());
        while (false !== $row = $stmt->fetch()) {
            yield $row['id'] => static::from_db_row($row);
        }
    }

    /**
     * @param (string|int|bool)[] $row
     * @return Offre
     */
    protected static abstract function from_db_row(array $row): Offre;

    private static function make_select(): string
    {
        assert(self::TABLE !== static::TABLE);
        return 'select * from ' . static::TABLE . ' o'
            . ' inner join ' . Adresse::TABLE . ' a on a.id = o.id_adresse'
            . ' inner join ' . Commune::TABLE . ' c on c.code = a.code_commune and c.numero_departement = a.numero_departement'
            . ' inner join ' . Image::TABLE . ' i on i.id = o.id_image_principale'
            . ' inner join ' . Professionnel::TABLE . ' p on p.id = o.id_professionnel';
    }
}
