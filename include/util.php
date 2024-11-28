<?php
/**
 * Cause une erreur si la valeur fournie est strictement égale à `false`.
 *
 * @template T
 * @param T|false $valeur La valeur à comparer à `false` avec l'opérateur `===`
 * @param string $message Le message d'erreur à afficher si $valeur était `false`
 * @return T $valeur si elle n'était pas strictement égale à `false`.
 * @throws DomainException Si $valeur est `false`.
 */
function notfalse(mixed $valeur, string $message = 'was false'): mixed
{
    if ($valeur === false) {
        throw new DomainException($message);
    }
    return $valeur;
}

/**
 * Supprime une clé d'un tableu et retourne la valeur associée.
 * @template T
 * @param T[] $array Le tableau à modifier.
 * @param string|int $key La clé à retirer. Elle doit exister dans le tableau.
 * @throws \DomainException Si la clé n'existe pas dans le tableau.
 * @return T La valeur associée à la clé retirée.
 */
function array_pop_key(array &$array, string|int $key): mixed
{
    if (!array_key_exists($key, $array)) {
        throw new DomainException("Array must contain key '$key'");
    }
    $value = $array[$key];
    unset($array[$key]);
    return $value;
}

/**
 * Détermine si tous les éléments d'un tableau satisfont un prédicat.
 * @template T
 * @param T[] $arr Le tableau à tester.
 * @param callable(T): bool $predicate La fonction prédicat à appeler avec chaque élément de $arr.
 * @return bool `true` si $predicate n'a retourné une valeur *falsey* pour aucun élément de $arr, false sinon.
 */
function array_every(array $arr, callable $predicate): bool
{
    foreach ($arr as $e) {
        if (!$predicate($e)) {
            return false;
        }
    }
    return true;
}

/**
 * Valide et récupère un argument d'un tableau source.
 * @param array $source Le tableau source d'où récupérer l'argument (tel que `$_GET`, `$_POST` ou `$_FILES$`).
 * @param string $nom Le nom de l'argument (clé dans le tableau) à récupérer.
 * @param ?callable(string, mixed): mixed $filter Un filtre optionnel à appliquer à la valeur, donné par `arg_check` ou `arg_filter`. Une erreur HTML est jetée si l'argument (la valeur de $source à la clé $nom) ne satisfait pas le filtre
 * @param bool $required Si cet argument est obligatoire. Si `true` et l'argument n'est pas présent, une erreur HTML est jetée.
 * @return mixed L'argument récupéré et potentiellement transformé (si $filter est non `null`). Si l'argument n'est pas requis ($required est `false`) et manquant (il n'y a pas de clé $nom dans $source), `null` est retourné.
 */
function getarg(array $source, string $nom, ?callable $filter = null, bool $required = true): mixed
{
    if (!isset($source[$nom]) || $source[$nom] === '') {
        if ($required) {
            html_error("argument manquant: $nom");
        } else {
            return null;
        }
    }
    return $filter === null ? $source[$nom] : $filter($nom, $source[$nom]);
}

/**
 * Crée un filtre pour `getarg` qui valide l'argumnet via prédicat.
 * @param callable(mixed): bool Une fonction prédicat.
 * @return callable(string, mixed): mixed Un filtre utilisable par la fonction `getarg`.
 */
function arg_check(callable $check): callable
{
    return function (string $name, mixed $value) use ($check) {
        if (!$check($value)) {
            html_error("argument $name invalide: " . var_export($value, true));
        }
        return $value;
    };
}

/**
 * Crée un filtre pour `getarg` qui applique un filtre PHP à l'argument agec la fonction `filter_var`.
 * @param int $filter Le filtre à appliquer à l'argument.
 * @param array|int $options Les options du filtre. Voir la documentation PHP pour les valeurs possibles.
 * @return callable(string, mixed): mixed Un filtre utilisable par la fonction `getarg`.
 */
function arg_filter(int $filter, array|int $options = 0): callable
{
    return function (string $name, mixed $value) use ($filter, $options) {
        $result = filter_var($value, $filter, $options);
        if ($result === false) {
            html_error("argument $name invalide: " . var_export($value, true));
        }
        return $result;
    };
}

/**
 * Affiche un message d'erreur HTML et jette une exception.
 * @param mixed $arg La valeur à inclure avec le message d'erreur. Si c'est une instance de `Throwable`, est est aussi jetée. Sinon, elle est englobée dans une `DomainException` puis jetée.
 * @throws DomainException
 */
function html_error(mixed $arg): never
{
    ?>
    <p>Erreur: <?= strval($arg) ?></p><?php
      if ($arg instanceof Throwable) {
          throw $arg;
      }
      throw new DomainException($arg);
}

/**
 * Concatène une chaîne à un suffixe ou retourne la chaîne vide.
 * @param ?string $chaine La chaîne (peut être `null`).
 * @param ?string $suffixe Le suffixe à concaténer à $chaine.
 * @return string La chaîne formatée.
 */
function elvis(?string $chaine, ?string $suffixe): string
{
    return $chaine ? "$chaine$suffixe" : '';
}

/**
 * Retourne une fonction prédicat qui vérifie qu'une chaîne est contenue dans une liste de valeurs autoriséess.
 * @param string[] $allowed_values Les valeurs autorisées.
 * @return callable(string): bool Une fonction prédicat validant une chaîne.
 */
function f_is_in(array $allowed_values): callable
{
    return fn($value) => array_search($value, $allowed_values) !== false;
}

/**
 * Returns a function prédicat qui vérifie qu'un tableau contient toutes les clés spécifiées
 * @param array $keys Les clés devant être dans le tableau.
 * @return callable(array): bool Un fonction prédicat validant un tableau.
 */
function f_array_has_keys(array $keys): callable
{
    return fn($value) => is_array($value) && array_every($keys, fn($key) => isset ($value[$key]));
}

/**
 * Retourne le seul élément d'un tableau.
 * @template T
 * @param array<T> $array Un tableau devant contenir extactement 1 élément.
 * @return T Le seul élément de $array.
 * @throws DomainException Si $array ne contient pas exactement 1 élément.
 */
function single(array $array): mixed
{
    if (count($array) !== 1) {
        throw new DomainException('Array contains not a single value');
    }
    return $array[0];
}

/**
 * Retourne le seul élément d'un tableau, ou une valeur par défaut si le tableau est vide.
 * @template T
 * @param array<T> $array Un tableau devant contenir 0 ou 1 élément.
 * @param T $default La valeur par défaut à retourner quand $array est vide.
 * @return T Le seul élément de $array, ou $default si $array est vide.
 * @throws DomainException Si le tableau contient plus d'une valeur.
 */
function single_or_default(array $array, mixed $default = null): mixed
{
    if (empty($array)) {
        return $default;
    }
    if (count($array) !== 1) {
        throw new DomainException('Array contains not a single value');
    }
    return $array[0];
}

/**
 * Converts a "structre of arrays" (SOA) to an "array of structures" (AOS).
 *
 * The input `$array` is expected to be an array where each element is an associative
 * array with the same set of keys. This function will return a new array where each
 * element is an array containing the values for each key from the input array.
 * @param array<array> $array The input "structre of arrays" to convert.
 * @return array<int, mixed> The resulting "array of structures".
 *
 * @example
 * Input: ['debut' => [1, 3], 'fin' => [2, 4]]
 * Output: [['debut' => 1, 'fin' => 2], ['debut' => 3, 'fin' => 4]]
 */
function soa_to_aos(array $array): array
{
    $result = [];
    foreach ($array as $key => $all) {
        foreach ($all as $i => $val) {
            $result[$i][$key] = $val;
        }
    }
    return $result;
}

/**
 * Formate une adresse dans un format humainement lisible.
 * @param array $adresse L'adresse (ligne issue de la BDD, voir `DB\query_adresse`)
 * @return string
 */
function format_adresse(array $adresse)
{
    // Concaténer les informations pour former une adresse complète

    return elvis($adresse['precision_ext'], ', ')
        . elvis($adresse['precision_int'], ', ')
        . elvis($adresse['numero_voie'], ' ')
        . elvis($adresse['complement_numero'], ' ')
        . elvis($adresse['nom_voie'], ', ')
        . elvis($adresse['localite'], ', ')
        . elvis(DB\query_commune($adresse['code_commune'], $adresse['numero_departement'])['nom'], ', ')
        . DB\query_codes_postaux($adresse['code_commune'], $adresse['numero_departement'])[0];
}
