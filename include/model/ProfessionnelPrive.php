<?php

final class ProfessionnelPrive extends Professionnel
{
    protected const FIELDS = parent::FIELDS + [
        'siren' => [[null, 'siren', PDO::PARAM_STR]],
    ];
    protected string $siren;

    function __construct(
        string $email,
        ?int $id,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        Adresse $adresse,
        string $denomination,
        string $siren,
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
        $this->siren = $siren;
    }
}
