<?php
require_once 'util.php';

/**
 * @template T Type du range.
 * Abstraction du type `<type>range` postgresql non vide.
 */
final class NonEmptyRange
{
    /*
     * From the PostgresQL doc :
     * > Each bound value can be quoted using " (double quote) characters. This is necessary if the bound value contains parentheses, brackets, commas, double quotes, or backslashes, since these characters would otherwise be taken as part of the range syntax. To put a double quote or backslash in a quoted bound value, precede it with a backslash. (Also, a pair of double quotes within a double-quoted bound value is taken to represent a double quote character, analogously to the rules for single quotes in SQL literal strings.) Alternatively, you can avoid quoting and use backslash-escaping to protect all data characters that would otherwise be taken as range syntax. Also, to write a bound value that is an empty string, write "", since writing nothing means an infinite bound.
     * >
     * > Whitespace is allowed before and after the range value, but any whitespace between the parentheses or brackets is taken as part of the lower or upper bound value. (Depending on the element type, it might or might not be significant.)
     *
     * This parsing algorithm does not support backslash-escaping in unquoted bounds nor additional characters outside quoted bounds.
     */

    // group 2 and 3 -> replace
    // \\ -> \
    // \" -> "
    // "" -> "
    private const READ_PATTERN = <<<'REGEX'
/^\s*([[(])(?|([^()[\],"\'\\]*)|"((?:[^"\\]|\\\\|\\"|"")*)"),(?|([^()[\],"\'\\]*)|"((?:[^"\\]|\\\\|\\"|"")*)")([])])/u
REGEX;

    private const PARSE_PATTERN = <<<'REGEX'
/^\s*([[(])(?|([^()[\],"\'\\]*)|"((?:[^"\\]|\\\\|\\"|"")*)"),(?|([^()[\],"\'\\]*)|"((?:[^"\\]|\\\\|\\"|"")*)")([])])\s*$/u
REGEX;

    private static function unescape_bound(string $parsed_bound): string
    {
        return str_replace(['\\\\', '\"', '""'], ['\\', '"', '"'], $parsed_bound);
    }

    /**
     * Si la borne inférieure est inclusive ou exclusive.
     * @var bool `true` si la borne inférieure est inclusive, `false` si elle est exclusive ou inexistante.
     */
    readonly bool $lower_inc;

    /**
     * Si la borne supérieure est inclusive ou exclusive.
     * @var bool `true` si la borne supérieure est inclusive, `false` si elle est exclusive ou inexistante.
     */
    readonly bool $upper_inc;

    /**
     * La borne inférieure ou `null` si elle est n'existe pas.
     * @var ?T
     */
    readonly mixed $lower;

    /**
     * La borne supérieure ou `null` si elle est n'existe pas.
     * @var ?T
     */
    readonly mixed $upper;

    function __construct(bool $lower_inc, mixed $lower, mixed $upper, bool $upper_inc)
    {
        if ($upper !== null && $lower !== null && $upper <= $lower) {
            throw new RangeException('Borne supérieure inférieure à la borne inférieure.');
        }

        $this->lower = $lower;
        $this->upper = $upper;
        $this->lower_inc = $lower !== null && $lower_inc;
        $this->upper_inc = $upper !== null && $upper_inc;
    }

    /**
     * Parse un `NonEmptyRange` à partir de la sortie PostgreSQL.
     * @template TBound
     * @param string $output La chaîne à parser.
     * @param callable(string): TBound|false $parse_bound La fonction parsant les bornes. Peut retourner `false` si la syntaxe est invalide.
     * @return NonEmptyRange<TBound> Un nouveau range non vide.
     * @throws DomainException Quand $output ne correspond pas à la syntax attendue.
     */
    static function parse(string $output, callable $parse_bound): NonEmptyRange
    {
        $match = null;
        if (!preg_match(self::PARSE_PATTERN, $output, $match, 0)) {
            throw new DomainException();
        }
        return notfalse(self::from_match($match, $parse_bound));
    }

    /*
     * parse() method:
     *
     * Expects the input string to contain ONLY the range expression
     * Must match the entire string exactly
     * Throws a DomainException if the string doesn't match the expected format
     * Returns a complete NonEmptyRange object
     * Best used when you know you have an isolated range string
     *
     * read() method:
     *
     * Can handle input strings that contain the range expression plus additional content after it
     * Only needs to match at the start of the string
     * Returns false if no match is found instead of throwing an exception
     * Returns both the parsed NonEmptyRange object AND the number of characters read
     * Better suited for parsing ranges that might be embedded in larger text or when you need to read multiple ranges sequentially
     * For example:
     *
     * parse("[1,5]") would work
     * parse("[1,5] extra stuff") would fail
     * read("[1,5] extra stuff") would work and tell you it read 5 characters
     * read("invalid") would return false
     * The read() method is more flexible and forgiving, while parse() is stricter and ensures the entire input is a valid range format.
     */

    /**
     * Lit un `NonEmptyRange` à partir de la sortie PostgreSQL.
     * @template TBound
     * @param string $source La chaîne d'où lire. Doit commencer par le range, mais peut être plus longue.
     * @param callable(string): TBound|false $parse_bound La fonction parsant les bornes. Peut returner `false` si la syntaxe est invalide.
     * @return array{NonEmptyRange<TBound>, int}|false Le couple du nouveau range non vide et du nombre de caractères lus, ou `false` en cas d'erreur.
     */
    static function read(string $source, callable $parse_bound): array|false
    {
        $match = null;
        if (!preg_match(self::READ_PATTERN, $source, $match, 0)) {
            return false;
        }
        $range = self::from_match($match, $parse_bound);
        return $range === false ? false : [$range, strlen($match[0])];
    }

    private static function from_match(array $match, callable $parse_bound): NonEmptyRange|false
    {
        [, $lower_delim, $lower, $upper, $upper_delim] = $match;
        $lower = $lower === '' ? null : $parse_bound(self::unescape_bound($lower));
        $upper = $upper === '' ? null : $parse_bound(self::unescape_bound($upper));
        if ($lower === false || $upper === false) {
            return false;
        }
        return new self(
            $lower_delim === '[',
            $lower,
            $upper,
            $upper_delim === ']',
        );
    }
}
