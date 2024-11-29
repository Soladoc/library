<?php
require_once 'util.php';
require_once 'model/Commune.php';
require_once 'model/Signalable.php';
require_once 'model/Identite.php';

abstract class Compte extends Identite implements Signalable
{
    private ?int $id;
    readonly string $email;
    readonly string $mdp_hash;
    readonly string $nom;
    readonly string $prenom;
    readonly string $telephone;
    readonly Adresse $adresse;

    function __construct(
        ?int $id,
        string $email,
        string $mdp_hash,
        string $nom,
        string $prenom,
        string $telephone,
        Adresse $adresse,
    ) {
        parent::__construct($id);
        $this->email = $email;
        $this->mdp_hash = $mdp_hash;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->adresse = $adresse;
    }
}
