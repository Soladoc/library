<?php
require_once 'util.php';
require_once 'model/Commune.php';
require_once 'model/Signalable.php';
require_once 'model/Identite.php';

/**
 * @inheritDoc
 */
abstract class Compte extends Identite implements Signalable
{
    protected const FIELDS = [
        'email'     => [[null, 'email',      PDO::PARAM_STR]],
        'mdp_hash'  => [[null, 'mdp_hash',   PDO::PARAM_STR]],
        'nom'       => [[null, 'nom',        PDO::PARAM_STR]],
        'prenom'    => [[null, 'prenom',     PDO::PARAM_STR]],
        'telephone' => [[null, 'telephone',  PDO::PARAM_STR]],
        'adresse'   => [['id', 'id_adresse', PDO::PARAM_STR]],
    ];

    function __construct(
        ?int $id,
        readonly string $email,
        readonly string $mdp_hash,
        readonly string $nom,
        readonly string $prenom,
        readonly string $telephone,
        readonly Adresse $adresse,
    ) {
        parent::__construct($id);
    }

    const TABLE = '_compte';
}
