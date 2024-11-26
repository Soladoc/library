<?php

require_once 'util.php';
require_once 'model/NonEmptyRange.php';

/**
 * @template T Type du multirange.
 * Abstraction du type PostgreSQL `<type>multirange`
 * 
 * N'effectue pas de normalisation.
 */
final class MultiRange
{
    /**
     * The ranges nested in this multirange.
     * @var NonEmptyRange[]
     */
    readonly array $ranges;

    /**
     * @param NonEmptyRange[] $ranges
     */
    function __construct(array $ranges)
    {
        $this->ranges = $ranges;
    }

    function __tostring(): string
    {
        return '{' . implode(',', $this->ranges) . '}';
    }

    /**
     * Parse un `MultiRange` à partir de la sortie PostgreSQL.
     * @template TBound
     * @param string $output La chaîne représentant un `TimeMultiRange` retournée par PostgreSQL.
     * @param callable(string): TBound $parse_bound La fonction parsant les bornes. Peut jeter `DomainException` si la syntaxe est invalide.
     * @return MultiRange<TBound>
     * @throws DomainException Quand $output ne correspond pas à la syntax attendue.
     */
    public static function parse(string $output, callable $parse_bound): MultiRange
    {
        $output = trim($output);
        if (strlen($output) === 0 || $output[0] !== '{' || $output[-1] !== '}') {
            throw new DomainException();
        }
        $output = trim(substr($output, 1, -1));

        $ranges = [];
        if ($output !== '') {
            [$ranges[], $read_length] = notfalse(NonEmptyRange::read($output, $parse_bound));
            while ($read_length < strlen($output)) {
                while (ctype_space($output[$read_length])) {
                    ++$read_length;
                }
                if ($output[$read_length++] !== ',') {
                    throw new DomainException();
                }
                while (ctype_space($output[$read_length])) {
                    ++$read_length;
                }

                [$ranges[], $rl] = notfalse(NonEmptyRange::read(substr($output, $read_length), $parse_bound));
                $read_length += $rl;
            }
        }

        return new self($ranges);
    }
}
