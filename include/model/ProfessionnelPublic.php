<?php

final class ProfessionnelPublic extends Professionnel
{
    function __construct(string $email,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        Adresse $adresse,
        string $denomination,
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
    }
}
