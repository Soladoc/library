<?php

require_once 'db.php';

/**
 * @property-read ?int $id L'ID. `null` si cet avis n'existe pas dans la BDD.
 * @property-read ?string $pseudo_auteur Calculé. `null` si cet avis n'existe pas dans la BDD.
 * @property-read ?FiniteTimestamp $publie_le Calculé. `null` si cet avis n'existe pas dans la BDD.
 * @property-read ?bool $lu Calculé. `null` si cet avis n'existe pas dans la BDD.
 * @property-read ?bool $blackliste Calculé. `null` si cet avis n'existe pas dans la BDD.
 */
class Avis extends Model
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
            'pseudo_auteur' => [null, 'pseudo_auteur', PDO::PARAM_STR],
            'publie_le'     => [FiniteTimestamp::parse(...), 'publie_le', PDO::PARAM_STR],
            'lu'            => [null, 'lu',         PDO::PARAM_BOOL],
            'blackliste'    => [null, 'blackliste', PDO::PARAM_BOOL],
        ];
    }

    protected static function fields()
    {
        return [
            'commentaire'      => [null, 'commentaire',     PDO::PARAM_STR],
            'note'             => [null, 'note',            PDO::PARAM_INT],
            'date_experience'  => [null, 'date_experience', PDO::PARAM_STR],
            'contexte'         => [null, 'contexte',        PDO::PARAM_STR],
            'id_membre_auteur' => [fn($x) => $x->id, 'membre_auteur', PDO::PARAM_INT],
            'id_offre'         => [fn($x) => $x->id, 'offre',         PDO::PARAM_INT],
        ];
    }

    function __construct(
        protected ?int $id,
        readonly string $commentaire,
        readonly int $note,
        readonly Date $date_experience,
        readonly string $contexte,
        readonly Membre $membre_auteur,
        readonly Offre $offre,
        //
        protected ?bool $blackliste           = null,
        protected ?bool $lu                   = null,
        protected ?string $pseudo_auteur      = null,
        protected ?FiniteTimestamp $publie_le = null,
    ) {}

    static function from_db(int $id_avis): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where a.id = ?'));
        DB\bind_values($stmt, [1 => [$id_avis, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }

    /**
     * Retourne le seul avis qu'un membre est autorisé à publier sur une offre, ou `false` si le membre n'a pas encore déposé d'avis.
     * @param int $id_membre_auteur
     * @param int $id_offre
     * @return Avis|false
     */
    static function from_db_single(int $id_membre_auteur, int $id_offre): self|false
    {
        $stmt = notfalse(DB\connect()->prepare(self::make_select() . ' where id_membre_auteur = ? and id_offre = ?'));
        DB\bind_values($stmt, [1 => [$id_membre_auteur, PDO::PARAM_INT], 2 => [$id_offre, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }

    private static function from_db_row(array $row): self
    {
        self::require_subclasses();
        $args_avis = [
            $row['id'],
            $row['commentaire'],
            $row['note'],
            $row['date_experience'],
            $row['contexte'],
            Membre::from_db($row['id_membre_auteur']),
            Offre::from_db($row['id_offre']),
            $row['lu'],
            $row['blackliste'],
            $row['pseudo_auteur'],
            $row['publie_le'],
        ];

        $id_restaurant = $row['id_restaurant'] ?? null;
        return $id_restaurant
            ? new AvisRestaurant(
                $args_avis,
                $row['note_cuisine'],
                $row['note_service'],
                $row['note_ambiance'],
                $row['note_qualite_prix'],
            )
            : new self(...$args_avis);
    }

    private static function make_select(): string
    {
        self::require_subclasses();
        // todo: faire des jointures pour gagner en performance
        return 'select
            a.id,
            a.commentaire,
            a.note,
            a.date_experience,
            a.contexte,
            a.id_membre_auteur,
            a.id_offre,
            a.lu,
            a.blackliste,
            a.pseudo_auteur,
            a.publie_le

            v.id_restaurant,
            v.note_cuisine,
            v.note_service,
            v.note_ambiance,
            v.note_qualite_prix
         from ' . self::TABLE . ' a
            left join ' . AvisRestaurant::TABLE . ' v using (id)';
    }

    private static function require_subclasses(): void
    {
        require_once 'model/AvisRestaurant.php';
    }

    const TABLE = 'avis';
}
