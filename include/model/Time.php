<?php

require_once 'util.php';

/**
 * Abstraction du type TIME PostgreSQL
 */
final class Time
{
    private const FORMAT = 'H:i:s';

    private readonly DateTimeImmutable $datetime;

    private function __construct(DateTimeImmutable $datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * Parse une heure depuis la sortie PostgreSQL.
     * @param ?string $output La sortie PostgreSQL.
     * @return ?Time Un nouvelle heure, ou `null` si `$output` était `null` (à l'instar de PostgreSQL, cette fonction propage `null`)
     * @throws DomainException En cas de mauvaise syntaxe.
     */
    public static function parse(?string $output): ?Time
    {
        return $output === null ? null : new self(notfalse(
            DateTimeImmutable::createFromFormat(self::FORMAT, $output)
                ?: DateTimeImmutable::createFromFormat('H:i', $output)
        ));
    }

    function __toString(): string
    {
        return $this->datetime->format(self::FORMAT);
    }
}
