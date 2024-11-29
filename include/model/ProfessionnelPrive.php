<?php

final class Professionnelprive extends Professionnel
{
    readonly string $siren;
    function __construct(string $email,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        Adresse $adresse,
        string $denomination,
        string $siren,
        ?int $id)
    {
        parent::__construct(
            $email,
            $mdp_hash,
            $nom,
            $prenom,
            $telephone,
            $adresse,
            $denomination,
            $id,
        );
        $this->siren = $siren;
    }
}
