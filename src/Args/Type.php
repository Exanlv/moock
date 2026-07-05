<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Type
{
    public static function string(): Closure
    {
        return fn (mixed $actual): bool => is_string($actual);
    }

    public static function int(): Closure
    {
        return fn (mixed $actual): bool => is_int($actual);
    }

    public static function float(): Closure
    {
        return fn (mixed $actual): bool => is_float($actual);
    }

    public static function array(): Closure
    {
        return fn (mixed $actual): bool => is_array($actual);
    }

    public static function resource(): Closure
    {
        return fn (mixed $actual): bool => is_resource($actual);
    }

    public static function object(): Closure
    {
        return fn (mixed $actual): bool => is_object($actual);
    }

    public static function bool(): Closure
    {
        return fn (mixed $actual): bool => is_bool($actual);
    }

    public static function null(): Closure
    {
        return fn (mixed $actual): bool => $actual === null;
    }

    public static function callable(): Closure
    {
        return fn (mixed $actual): bool => is_callable($actual);
    }

    public static function iterable(): Closure
    {
        return fn (mixed $actual): bool => is_iterable($actual);
    }

    public static function scalar(): Closure
    {
        return fn (mixed $actual): bool => is_scalar($actual);
    }

    public static function instanceOf(string $expected): Closure
    {
        return fn (mixed $actual): bool => is_object($actual) && $actual instanceof $expected;
    }
}
