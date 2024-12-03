<?php
require_once 'util.php';
require_once 'model/Commune.php';
require_once 'model/Signalable.php';
require_once 'model/Identite.php';

/**
 * @inheritDoc
 * @property string $email
 * @property string $mdp_hash
 * @property string $nom
 * @property string $prenom
 * @property string $telephone
 * @property Adresse $adresse
 */
abstract class Compte extends Identite implements Signalable
{
    protected const FIELDS = [
        'email'     => [[null, 'email',      PDO::PARAM_STR]],
        'mdp_hash'  => [[null, 'mdp_hash',   PDO::PARAM_STR]],
        'nom'       => [[null, 'nom',        PDO::PARAM_STR]],
        'prenom'    => [[null, 'prenom',     PDO::PARAM_STR]],
        'telephone' => [[null, 'telephone',  PDO::PARAM_STR]],
        'adresse'   => [[null, 'id_adresse', PDO::PARAM_STR]],
    ];

    protected string $email;
    protected string $mdp_hash;
    protected string $nom;
    protected string $prenom;
    protected string $telephone;
    protected Adresse $adresse;

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
        $this->email     = $email;
        $this->mdp_hash  = $mdp_hash;
        $this->nom       = $nom;
        $this->prenom    = $prenom;
        $this->telephone = $telephone;
        $this->adresse   = $adresse;
    }
}
