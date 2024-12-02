<?php

final class Commune
{
    const TABLE = '_commune';

    readonly int $code;
    readonly string $nom;
    readonly string $numero_departement;

    private function __construct(int $code, string $numero_departement, string $nom)
    {
        $this->code = $code;
        $this->nom = $nom;
        $this->numero_departement = $numero_departement;
    }

    static function from_db_by_nom(string $nom): Commune|false
    {
        $stmt = notfalse(DB\connect()->prepare('select code, numero_departement from ' . self::TABLE . ' where nom = ?'));
        DB\bind_values($stmt, [1 => [$nom, PDO::PARAM_STR]]);
        notfalse($stmt->execute());
        $row = $stmt->fetch();
        return $row === false ? false : new Commune($row['code'], ltrim($row['numero_departement']), $nom);
    }

    static function from_db(int $code, string $numero_departement): Commune|false
    {
        $stmt = notfalse(DB\connect()->prepare('select nom from ' . self::TABLE . ' where code = ? and numero_departement = ?'));
        DB\bind_values($stmt, [1 => [$code, PDO::PARAM_INT], 2 => [$numero_departement, PDO::PARAM_STR]]);
        notfalse($stmt->execute());
        $nom = $stmt->fetchColumn();
        return $nom === false ? false : new Commune($code, $numero_departement, $nom);
    }
    /**
     * Retourne les code postaux de cette commune.
     * @return string[] Les codes postaux de cette commune.
     */
    function code_postaux(): array
    {
        // todo: cache this result
        $stmt = notfalse(DB\connect()->prepare('select code_postal from _code_postal where code_commune = ? and numero_departement = ?'));
        DB\bind_values($stmt, [1 => [$this->code, PDO::PARAM_INT], 2 => [$this->numero_departement, PDO::PARAM_STR]]);
        notfalse($stmt->execute());
        return array_column($stmt->fetchAll(), 'code_postal');
    }
}
