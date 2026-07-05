<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Type
{
    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function string(): Closure
    {
        return fn (mixed $actual): bool => is_string($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function int(): Closure
    {
        return fn (mixed $actual): bool => is_int($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function float(): Closure
    {
        return fn (mixed $actual): bool => is_float($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function array(): Closure
    {
        return fn (mixed $actual): bool => is_array($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function resource(): Closure
    {
        return fn (mixed $actual): bool => is_resource($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function object(): Closure
    {
        return fn (mixed $actual): bool => is_object($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function bool(): Closure
    {
        return fn (mixed $actual): bool => is_bool($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function null(): Closure
    {
        return fn (mixed $actual): bool => $actual === null;
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function callable(): Closure
    {
        return fn (mixed $actual): bool => is_callable($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function iterable(): Closure
    {
        return fn (mixed $actual): bool => is_iterable($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function scalar(): Closure
    {
        return fn (mixed $actual): bool => is_scalar($actual);
    }

    /**
     * @psalm-return Closure(mixed):bool
     */
    public static function instanceOf(string $expected): Closure
    {
        return fn (mixed $actual): bool => is_object($actual) && $actual instanceof $expected;
    }
}
