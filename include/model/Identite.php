<?php

/**
 * @property-read ?int $id L'ID. `null` si cette identité n'existe pas dans la BDD.
 */
abstract class Identite {
    function __get(string $name) {
        return match($name) {
            'id' => $this->id,
        };
    }

    private ?int $id;

    function __construct(?int $id) {
        $this->id = $id;
    }
}