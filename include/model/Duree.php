<?php

final class Duree
{
    readonly int $jours;
    readonly int $heures;
    readonly int $minutes;

    private function __construct(array $data)
    {
        $this->jours = $data['jours'];
        $this->heures = $data['heures'];
        $this->minutes = $data['minutes'];
    }

    static function from_input(array $data): Duree {
        return new Duree($data);
    }

    /**
     * Format la durée en INTERVAL PostgreSQL.
     * @return string La durée formatée en une chaîne d'entrée valide pour le type INTERVAL PostgreSQL.
     */
    function format(): string
    {
        return static::make_interval(
            $this->jours,
            $this->heures,
            $this->minutes,
        );
    }

    private static function make_interval(int $days, int $hours, int $mins)
    {
        $stmt = notfalse(DB\connect()->prepare('select make_interval(days => ?, hours => ?, mins => ?)'));
        DB\bind_values($stmt, [1 => [$days, PDO::PARAM_INT], 2 => [$hours, PDO::PARAM_INT], 3 => [$mins, PDO::PARAM_INT]]);
        notfalse($stmt->execute());
        return notfalse($stmt->fetchColumn());
    }
}
