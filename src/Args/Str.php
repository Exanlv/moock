<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Str
{
    public static function length(int $expectedLength): Closure
    {
        return function (string $actual) use ($expectedLength): bool {
            return strlen($actual) === $expectedLength;
        };
    }

    public static function contains(string $needle): Closure
    {
        return function (string $actual) use ($needle): bool {
            return str_contains($actual, $needle);
        };
    }
}
