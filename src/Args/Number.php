<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Number
{
    public static function lt(int|float $lessThan): Closure
    {
        return function (int|float $actual) use ($lessThan): bool {
            return $actual < $lessThan;
        };
    }

    public static function gt(int|float $greaterThan): Closure
    {
        return function (int|float $actual) use ($greaterThan): bool {
            return $actual > $greaterThan;
        };
    }

    public static function range(int|float $min, int|float $max): Closure
    {
        return function (int|float $actual) use ($min, $max): bool {
            return $actual >= $min && $actual <= $max;
        };
    }
}
