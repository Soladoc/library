<?php

require_once 'util.php';

/**
 * Abstraction du type PostgesQL TIMESTAMP (WITHOUT TIME ZONE). 
 */
final class Timestamp {
    private const FORMAT = 'Y-m-d H:i:s';
    private readonly DateTimeImmutable $datetime;

    private function __construct(DateTimeImmutable $datetime) {
        $this->datetime = $datetime;
    }

    static function parse(string $timestamp): Timestamp {
        return new self(notfalse(DateTimeImmutable::createFromFormat(self::FORMAT, $timestamp)));
    }

    function __toString(): string {
        return $this->datetime->format(self::FORMAT);
    }
}