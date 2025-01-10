<?php
final class SouscriptionOption
{
    private function __construct(
        readonly bool $actif,
        readonly string $nom,
        readonly FiniteTimestamp $lancee_le,
        readonly int $nb_semaines,
        readonly float $prix_hebdomadaire,
    ) {}

    static function parse_json(?string $json_output): ?SouscriptionOption {
        if ($json_output === null) return null;
        [$actif, $nom, $lancee_le, $nb_semaines, $prix_hebdomadaire] = json_decode($json_output);
        return new SouscriptionOption($actif, $nom, FiniteTimestamp::parse($lancee_le), $nb_semaines, $prix_hebdomadaire);
    }
}
