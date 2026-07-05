<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Str
{
    /**
     * @psalm-return Closure(string):bool
     */
    public static function length(int $expectedLength): Closure
    {
        return fn (string $actual): bool => strlen($actual) === $expectedLength;
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function contains(string $needle): Closure
    {
        return fn (string $actual): bool => str_contains(strtolower($actual), strtolower($needle));
    }
}
