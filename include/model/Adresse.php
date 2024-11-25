<?php
require_once 'util.php';
require_once 'model/Commune.php';

/**
 * @property-read ?int $name L'ID. `null` si cette adresse n'existe pas dans la BDD.
 */
final class Adresse
{
    private const TABLE = '_adresse';

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

    function __get(string $name) {
        return match ($name) {
            'id' => $this->id,
            default => throw new Exception("Invalid property: '$name'"),
        };
    }

    private function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->commune = $data['commune'];
        $this->numero_voie = $data['numero_voie'] ?: null;
        $this->complement_numero = $data['complement_numero'] ?: null;
        $this->nom_voie = $data['nom_voie'] ?: null;
        $this->localite = $data['localite'] ?: null;
        $this->precision_int = $data['precision_int'] ?: null;
        $this->precision_ext = $data['precision_ext'] ?: null;
        $this->latitude = $data['latitude'] ?: null;
        $this->longitude = $data['longitude'] ?: null;
    }

    /**
     * Construit une adresse à partir des données produites par le composant d'input.
     * @param array $data Les données produites par le composant d'input d'adresse.
     * @return Adresse Une nouvelle adresse, non-existante dans la BDD.
     */
    static function from_input(array $data): Adresse
    {
        $data['commune'] = notfalse(Commune::from_db_by_nom($data['commune']));
        return new Adresse($data);
    }

    /**
     * Récupère une adresse depuis la base de données.
     * @param int $id_adresse L'ID de l'adresse à récupérer.
     * @return Adresse Une adresse existante dans la base de donneées, ou `false` si il n'existe pas d'adresse d'ID $id_adresse.
     */
    static function from_db(int $id_adresse): Adresse|false
    {
        $stmt = notfalse(DB\connect()->prepare('select * from ' . self::TABLE . ' where id = ?'));
        DB\bind_values($stmt, [1 => [$id_adresse, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : self::from_db_row($row);
    }

    private static function from_db_row(array $row): Adresse
    {
        $row['commune'] = Commune::from_db($row['code_commune'], $row['numero_departement']);
        return new Adresse($row);
    }

    /**
     * Pousse cette adresse dans la BDD, en l'insérant si elle n'existe pas ou en la mettant à jour.
     */
    function push_to_db(?int $id_adresse = null): void
    {
        $args = DB\filter_null_args([
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
        ]);
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
