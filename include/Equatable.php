<?php

/**
 * @template T
 */
interface Equatable {
    /**
     * Is this object equal to another object?
     * @param T $other
     * @return bool
     */
    function equals(mixed $other): bool;
}