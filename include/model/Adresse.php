<?php

require_once 'util.php';
require_once 'model/Commune.php';

/**
 * @property-read ?int $id L'ID. `null` si cette adresse n'existe pas dans la BDD.
 */
final class Adresse
{
    function __get(string $name)
    {
        return match ($name) {
            'id' => $this->id,
        };
    }

    const TABLE = '_adresse';

    /**
     * La commune.
     * @var Commune
     */
    public Commune $commune;

    /**
     * Le numéro de voie.
     * @var ?int
     */
    public ?int $numero_voie;

    /**
     * Le complément du numéro de voie.
     * @var ?string
     */
    public ?string $complement_numero;

    /**
     * Le nom de voie.
     * @var ?string
     */
    public ?string $nom_voie;

    /**
     * La localité (hameau/lieu-dit).
     * @var ?string
     */
    public ?string $localite;

    /**
     * La précision interne.
     * @var ?string
     */
    public ?string $precision_int;

    /**
     * La précision externe.
     * @var ?string
     */
    public ?string $precision_ext;

    /**
     * La latitude.
     * @var ?float
     */
    public ?float $latitude;

    /**
     * La longitude.
     * @var ?float
     */
    public ?float $longitude;

    private ?int $id;

    function __construct(
        ?int $id,
        Commune $commune,
        ?int $numero_voie,
        ?string $complement_numero,
        ?string $nom_voie,
        ?string $localite,
        ?string $precision_int,
        ?string $precision_ext,
        ?float $latitude,
        ?float $longitude,
    ) {
        $this->id = $id;
        $this->commune = $commune;
        $this->numero_voie = $numero_voie;
        $this->complement_numero = $complement_numero;
        $this->nom_voie = $nom_voie;
        $this->localite = $localite;
        $this->precision_int = $precision_int;
        $this->precision_ext = $precision_ext;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Récupère une adresse depuis la base de données.
     * @param int $id_adresse L'ID de l'adresse à récupérer.
     * @return Adresse Une adresse existante dans la base de donneées, ou `false` si il n'existe pas d'adresse d'ID $id_adresse.
     */
    static function from_db(int $id_adresse): Adresse|false
    {
        $stmt = notfalse(DB\connect()->prepare('select
            a.numero_voie,
            a.complement_numero,
            a.nom_voie,
            a.localite,
            a.precision_int,
            a.precision_ext,
            a.latitude,
            a.longitude,
            a.numero_departement,
            a.code_commune,
            c.nom from '
            . self::TABLE . ' a inner join ' . Commune::TABLE
            . ' c on a.numero_departement = c.numero_departement'
            . ' and code_commune = code'
            . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_adresse, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        if ($row === false) return false;
        $row['id'] = $id_adresse;
        return self::from_db_row($row);
    }

    /**
     * @param (string|int|bool)[] $row
     * @return Adresse
     */
    static function from_db_row(array $row, string $id_column = 'id'): Adresse
    {
        return new Adresse(
            $row[$id_column],
            Commune::from_db_row($row, 'code_commune'),
            $row['numero_voie'] ?? null,
            $row['complement_numero'] ?? null,
            $row['nom_voie'] ?? null,
            $row['localite'] ?? null,
            $row['precision_int'] ?? null,
            $row['precision_ext'] ?? null,
            $row['latitude'] ?? null,
            $row['longitude'] ?? null,
        );
    }

    /**
     * Pousse cette adresse vers la BDD, soit l'insérant, soit en la mettant à jour si elle y existe déjà.
     */
    function push_to_db(): void
    {
        $args = [
            'code_commune' => [$this->commune->code, PDO::PARAM_INT],
            'numero_departement' => [$this->commune->numero_departement, PDO::PARAM_INT],
            'numero_voie' => [$this->numero_voie, PDO::PARAM_INT],
            'complement_numero' => [$this->complement_numero, PDO::PARAM_INT],
            'nom_voie' => [$this->nom_voie, PDO::PARAM_STR],
            'localite' => [$this->localite, PDO::PARAM_STR],
            'precision_int' => [$this->precision_int, PDO::PARAM_STR],
            'precision_ext' => [$this->precision_ext, PDO::PARAM_STR],
            'latitude' => [$this->latitude, DB\PDO_PARAM_DECIMAL],
            'longitude' => [$this->longitude, DB\PDO_PARAM_DECIMAL],
        ];
        if ($this->id === null) {
            $stmt = DB\insert_into_returning_id(self::TABLE, $args);
            notfalse($stmt->execute());
            $this->id = $stmt->fetchColumn();
        } else {
            $stmt = DB\update(self::TABLE, $args, [
                'id' => [$this->id, PDO::PARAM_INT]
            ]);
            notfalse($stmt->execute());
        }
    }

    /**
     * Supprime cette adresse de la BDD.
     * @throws LogicException Quand adresse n'existe pas dans la BDD.
     * @return void
     */
    function delete_from_db(): void
    {
        if ($this->id === null) {
            throw new LogicException("Cette adresse n'existe pas dans la BDD et ne peut donc pas être supprimée");
        }
        $stmt = notfalse(DB\connect()->prepare('delete from ' . self::TABLE . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$this->id, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $this->id = null;
    }

    function format(): string
    {
        return elvis($this->precision_ext, ', ')
            . elvis($this->precision_int, ', ')
            . elvis($this->numero_voie, ' ')
            . elvis($this->complement_numero, ' ')
            . elvis($this->nom_voie, ', ')
            . elvis($this->localite, ', ')
            . elvis($this->commune->nom, ', ')
            . $this->commune->code_postaux()[0];
    }
}
