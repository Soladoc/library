<?php
final class SouscriptionOption
{
    private function __construct(
        readonly bool $actif,
        readonly string $nom,
        readonly FiniteTimestamp $lancee_le,
        readonly int $nb_semaines,
        readonly float $prix,
    ) {}

    static function parse_json(?string $json_output): ?SouscriptionOption {
        return $json_output === null ? null : new SouscriptionOption(...json_decode($json_output));
    }
}
