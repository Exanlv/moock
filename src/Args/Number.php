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
}
