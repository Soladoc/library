<?php

final class ProfessionnelPublic extends Professionnel
{
    function __construct(
        string $email,
        ?int $id,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        Adresse $adresse,
        string $denomination,
    ) {
        parent::__construct(
            $id,
            $email,
            $mdp_hash,
            $nom,
            $prenom,
            $telephone,
            $adresse,
            $denomination,
        );
    }
}
