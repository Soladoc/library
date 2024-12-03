<?php

require_once 'util.php';
require_once 'model/Commune.php';
require_once 'model/Model.php';

/**
 * @property-read ?int $id L'ID. `null` si cette adresse n'existe pas dans la BDD.
 * @property Commune $commune
 * @property ?int $numero_voie
 * @property ?string $complement_numero
 * @property ?string $nom_voie
 * @property ?string $localite
 * @property ?string $precision_int
 * @property ?string $precision_ext
 * @property ?float $latitude
 * @property ?float $longitude
 * @property ?int $id
 */
final class Adresse extends Model
{
    protected const FIELDS = [
        'commune' => [
            ['code',               'code_commune',       PDO::PARAM_INT],
            ['numero_departement', 'numero_departement', PDO::PARAM_STR]
        ],
        'numero_voie' => [[null,       'numero_voie',       PDO::PARAM_STR]],
        'complement_numero' => [[null, 'complement_numero', PDO::PARAM_STR]],
        'nom_voie' => [[null,          'nom_voie',          PDO::PARAM_STR]],
        'localite' => [[null,          'localite',          PDO::PARAM_STR]],
        'precision_int' => [[null,     'precision_int',     PDO::PARAM_STR]],
        'precision_ext' => [[null,     'precision_ext',     PDO::PARAM_STR]],
        'latitude' => [[null,          'latitude',          PDO::PARAM_STR]],
        'longitude' => [[null,         'longitude',         PDO::PARAM_STR]],
    ];

    protected static function key_fields()
    {
        return [
            'id' => ['id', PDO::PARAM_INT, null],
        ];
    }

    protected Commune $commune;
    protected ?int $numero_voie;
    protected ?string $complement_numero;
    protected ?string $nom_voie;
    protected ?string $localite;
    protected ?string $precision_int;
    protected ?string $precision_ext;
    protected ?float $latitude;
    protected ?float $longitude;
    protected ?int $id;

    function __construct(
        ?int $id,
        Commune $commune,
        ?int $numero_voie          = null,
        ?string $complement_numero = null,
        ?string $nom_voie          = null,
        ?string $localite          = null,
        ?string $precision_int     = null,
        ?string $precision_ext     = null,
        ?float $latitude           = null,
        ?float $longitude          = null,
    ) {
        $this->id                = $id;
        $this->commune           = $commune;
        $this->numero_voie       = $numero_voie;
        $this->complement_numero = $complement_numero;
        $this->nom_voie          = $nom_voie;
        $this->localite          = $localite;
        $this->precision_int     = $precision_int;
        $this->precision_ext     = $precision_ext;
        $this->latitude          = $latitude;
        $this->longitude         = $longitude;
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
        return $row === false ? false : new Adresse(
            $id_adresse,
            new Commune($row['code_commune'], $row['numero_departement'], $row['nom']),
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

    const TABLE = '_adresse';
}
