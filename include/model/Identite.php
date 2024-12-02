<?php

require_once 'model/Model.php';

/**
 * @property-read ?int $id L'ID. `null` si cette identité n'existe pas dans la BDD.
 */
abstract class Identite extends Model {
    protected ?int $id;

    function __construct(?int $id) {
        $this->id = $id;
    }
}