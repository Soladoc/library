<?php

abstract class Identite {
    private ?int $id;

    function __construct(?int $id) {
        $this->id = $id;
    }
}