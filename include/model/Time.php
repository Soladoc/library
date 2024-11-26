<?php

require_once 'util.php';

/**
 * Abstraction du type TIME PostgreSQL
 */
final class Time
{
    private const FORMAT = "H:i:s";

    private readonly DateTimeImmutable $datetime;

    private function __construct(DateTimeImmutable $datetime) {
        $this->datetime = $datetime;
    }

    public static function parse(string $time): Time {
        return new self(notfalse(
            DateTimeImmutable::createFromFormat(self::FORMAT, $time)
            ?: DateTimeImmutable::createFromFormat("H:i", $time)
        ));
    }

    function __toString(): string {
        return $this->datetime->format(self::FORMAT);
    }
}