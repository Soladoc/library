<?php

final class Duree
{
    readonly int $years;
    readonly int $months;
    readonly int $days;
    readonly int $hours;
    readonly int $minutes;
    readonly float $seconds;

    function __construct(
        int $years = 0,
        int $months = 0,
        int $days = 0,
        int $hours = 0,
        int $minutes = 0,
        float $seconds = 0,
    ) {
        $this->years = $years;
        $this->months = $months;
        $this->days = $days;
        $this->hours = $hours;
        $this->minutes = $minutes;
        $this->seconds = $seconds;
    }

    function __toString(): string
    {
        return "$this->years years $this->months mons $this->days days $this->hours:$this->minutes:$this->seconds";
    }

    /**
     * Parse une durée depuis la sortie PostgreSQL.
     * @param ?string $output La sortie PostgreSQL en format d'intervalle `postgres`.
     * @return ?Duree Un nouvelle durée, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws DomainException En cas de mauvaise syntaxe.
     */
    static function parse(?string $output): ?Duree
    {
        if ($output === null) return null;

        $matches = [];
        $years = 0;
        $months = 0;
        $days = 0;
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
        if (notfalse(preg_match('/(\d+) years/', $output, $matches)) === 1
            && false === ($years = parse_int($matches[1]))
            || notfalse(preg_match('/(\d+) mons/', $output, $matches)) === 1
            && false === ($months = parse_int($matches[1]))
            || notfalse(preg_match('/(\d+) days/', $output, $matches)) === 1
            && false === ($days = parse_int($matches[1]))
            || notfalse(preg_match('/(\d+):(\d+):(\d*\.?\d+)/', $output, $matches)) === 1
            && (false === ($hours = parse_int($matches[1], 0))
                || false === ($minutes = parse_int($matches[2], 0))
                || false === ($seconds = parse_float($matches[3], 0)))) {
            throw new DomainException();
        }
        return new Duree($years, $months, $days, $hours, $minutes, $seconds);
    }
}
