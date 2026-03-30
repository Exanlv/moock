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
}
