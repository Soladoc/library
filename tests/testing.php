<?php

require_once 'util.php';

function f_never_called(): callable
{
    return fn(...$_) => assert(false, "I wasn't supposed to be called");
}

function assert_strictly_equal(mixed $a, mixed $b): void
{
    assert($a === $b, var_export($a, true) . ' === ' . var_export($b, true));
}

function assert_equal(mixed $a, mixed $b): void
{
    assert($a == $b, var_export($a, true) . ' == ' . var_export($b, true));
}

/**
 * @param callable(): void $action
 * @param callable(Throwable): bool $exception_predicate
 * @return void
 */
function assert_throws(callable $action, callable $exception_predicate): void
{
    try {
        $action();
        assert(false, 'An exception was expected to be thrown here');
    } catch (Throwable $e) {
        assert($exception_predicate($e), 'The thrown exception does not match the predicate');
    }
}
