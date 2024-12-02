<?php

require_once 'util.php';

/**
 * Abstraction du type PostgeSQL TIMESTAMP (WITHOUT TIME ZONE) fini.
 */
final class FiniteTimestamp {
    private const FORMATS = ['Y-m-d H:i:s.u', 'Y-m-d H:i:s'];
    private readonly DateTimeImmutable $datetime;

    private function __construct(DateTimeImmutable $datetime) {
        $this->datetime = $datetime;
    }

    /**
     * Parse un timestamp fini depuis la sortie PostgreSQL.
     * @param ?string $output La sortie PostgreSQL.
     * @return ?FiniteTimestamp Un nouveau timestamp fini, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws DomainException En cas de mauvaise syntaxe.
     */
    static function parse(?string $output): ?FiniteTimestamp {
        return $output === null ? null
            : new self(notfalse(iterator_first_notfalse(self::FORMATS,
                fn($fmt) => DateTimeImmutable::createFromFormat($fmt, $output))));
    }

    function __toString(): string {
        return $this->datetime->format(self::FORMATS[0]);
    }
}