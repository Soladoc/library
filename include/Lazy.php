<?php

/**
 * @template T
 * @property-read T $value
 */
final class Lazy
{
    private mixed $value;

    function __get(string $name)
    {
        match ($name) {
            'value' => $this->value ??= ($this->get_value)(),
        };
    }

    /**
     * @var callable(): T
     */
    private $get_value;

    /**
     * @param callable(): T $get_value
     */
    function __construct(callable $get_value)
    {
        $this->get_value = $get_value;
    }
}
