<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Number
{
    public static function lt(int|float $lessThan): Closure
    {
        return fn (int|float $actual): bool => $actual < $lessThan;
    }

    public static function gt(int|float $greaterThan): Closure
    {
        return fn (int|float $actual): bool => $actual > $greaterThan;
    }

    public static function range(int|float $min, int|float $max): Closure
    {
        return fn (int|float $actual): bool => $actual >= $min && $actual <= $max;
    }

    public static function positive(): Closure
    {
        return fn (int|float $actual): bool => $actual > 0;
    }

    public static function negative(): Closure
    {
        return fn (int|float $actual): bool => $actual < 0;
    }

    public static function even(): Closure
    {
        return fn (int|float $actual): bool => is_int($actual) && $actual % 2 === 0;
    }

    public static function odd(): Closure
    {
        return fn (int|float $actual): bool => is_int($actual) && $actual % 2 !== 0;
    }

    public static function divisibleBy(int|float $divisor): Closure
    {
        return fn (int|float $actual): bool => $divisor != 0 && $actual % $divisor === 0;
    }

    public static function approx(float $target, float $epsilon = 0.00001): Closure
    {
        return fn (int|float $actual): bool => abs($actual - $target) <= $epsilon;
    }
}
