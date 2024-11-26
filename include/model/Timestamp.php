<?php

require_once 'util.php';

/**
 * Abstraction du type PostgesQL TIMESTAMP (WITHOUT TIME ZONE). 
 */
final class Timestamp {
    private readonly DateTimeImmutable $datetime;

    private function __construct(DateTimeImmutable $datetime) {
        $this->datetime = $datetime;
    }

    static function parse(string $timestamp): Timestamp {
        return new self(notfalse(DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $timestamp)));
    }

    function __toString(): string {
        return $this->datetime->format(DateTimeImmutable::ATOM);
    }
}