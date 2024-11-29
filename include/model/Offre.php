<?php
require_once 'model/Adresse.php';
require_once 'model/Image.php';
require_once 'model/Professionnel.php';
require_once 'db.php';

/**
 * Une offre touristique.
 */
abstract class Offre
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
     * La date de dernière mise à jour (incluant la création).
     * @var Timestamp
     */
    readonly Timestamp $modifiee_le;

    /**
     * La date de création de cette offre. Est égale à $modifee_le si cette offre n'a jamais été modifée.
     * @var Timestamp
     */
    readonly Timestamp $creee_le;

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

    readonly int $nb_avis;
    readonly float $note_moyenne;

    function __construct(
        string $titre,
        string $resume,
        string $description_detaillee,
        Adresse $adresse,
        Image $image_principale,
        int $nb_avis,
        float $note_moyenne,
        int $id,
    ) {
        $this->titre = $titre;
        $this->resume = $resume;
        $this->description_detaillee = $description_detaillee;
        $this->adresse = $adresse;
        $this->image_principale = $image_principale;
        $this->nb_avis = $nb_avis;
        $this->note_moyenne = $note_moyenne;
        $this->id = $id;
    }

    /**
     * Récupère les offres "À la Une" de la BDD.
     * @return Offre[] Les offres "À la Une" de la BDD.
     */
    static function from_db_a_la_une(): array
    {
        $stmt = notfalse(DB\connect()->prepare('select * from ' . self::TABLE . '
            left join ' . Visite::TABLE . ' using (id)
            left join _parc_attractions using (id)
            left join _restaurant using (id)
            left join _visite using (id)
            left join _spectacle using (id)
        '));
        notfalse($stmt->execute());
        return $stmt->fetchAll();
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
        return $stmt->fetchAll();
    }
}
