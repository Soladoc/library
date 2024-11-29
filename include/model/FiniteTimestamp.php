<?php

require_once 'util.php';

/**
 * Abstraction du type PostgeSQL TIMESTAMP (WITHOUT TIME ZONE) fini.
 */
final class FiniteTimestamp {
    private const FORMAT = 'Y-m-d H:i:s';
    private readonly DateTimeImmutable $datetime;

    private function __construct(DateTimeImmutable $datetime) {
        $this->datetime = $datetime;
    }

    /**
     * Parse un timestamp depuis la sortie PostgreSQL.
     * @param string $timestamp La sortie PostgreSQL
     * @return FiniteTimestamp Un nouveau timestamp.
     */
    static function parse(string $timestamp): FiniteTimestamp {
        return new self(notfalse(DateTimeImmutable::createFromFormat(self::FORMAT, $timestamp)));
    }

    function __toString(): string {
        return $this->datetime->format(self::FORMAT);
    }
}