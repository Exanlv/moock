<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Number
{
    /**
     * @psalm-return Closure(float|int):bool
     */
    public static function lt(int|float $lessThan): Closure
    {
        return fn (int|float $actual): bool => $actual < $lessThan;
    }

    /**
     * @psalm-return Closure(float|int):bool
     */
    public static function gt(int|float $greaterThan): Closure
    {
        return fn (int|float $actual): bool => $actual > $greaterThan;
    }

    /**
     * @psalm-return Closure(float|int):bool
     */
    public static function range(int|float $min, int|float $max): Closure
    {
        return fn (int|float $actual): bool => $actual >= $min && $actual <= $max;
    }

    /**
     * @psalm-return Closure(float|int):bool
     */
    public static function positive(): Closure
    {
        return fn (int|float $actual): bool => $actual > 0;
    }

    /**
     * @psalm-return Closure(float|int):bool
     */
    public static function negative(): Closure
    {
        return fn (int|float $actual): bool => $actual < 0;
    }

    /**
     * @psalm-return Closure(float|int):bool
     */
    public static function even(): Closure
    {
        return fn (int|float $actual): bool => is_int($actual) && $actual % 2 === 0;
    }

    /**
     * @psalm-return Closure(float|int):bool
     */
    public static function odd(): Closure
    {
        return fn (int|float $actual): bool => is_int($actual) && $actual % 2 !== 0;
    }

    /**
     * @psalm-return Closure(int):bool
     */
    public static function divisibleBy(int $divisor): Closure
    {
        return fn (int $actual): bool => $divisor != 0 && $actual % $divisor === 0;
    }

    /**
     * @psalm-return Closure(float|int):bool
     */
    public static function approx(float $target, float $epsilon = 0.00001): Closure
    {
        return fn (int|float $actual): bool => abs((float) $actual - $target) <= $epsilon;
    }
}
