<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Str
{
    public static function length(int $expectedLength): Closure
    {
        return fn (string $actual): bool => strlen($actual) === $expectedLength;
    }

    public static function contains(string $needle): Closure
    {
        return fn (string $actual): bool => str_contains(strtolower($actual), strtolower($needle));
    }
}
