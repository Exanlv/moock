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

    /**
     * @psalm-return Closure(string):bool
     */
    public static function startsWith(string $prefix): Closure
    {
        return fn (string $actual): bool => str_starts_with(strtolower($actual), strtolower($prefix));
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function endsWith(string $suffix): Closure
    {
        return fn (string $actual): bool => str_ends_with(strtolower($actual), strtolower($suffix));
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function matchesRegex(string $pattern): Closure
    {
        return fn (string $actual): bool => preg_match($pattern, $actual) === 1;
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function minLength(int $min): Closure
    {
        return fn (string $actual): bool => strlen($actual) >= $min;
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function maxLength(int $max): Closure
    {
        return fn (string $actual): bool => strlen($actual) <= $max;
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function alpha(): Closure
    {
        return fn (string $actual): bool => ctype_alpha($actual);
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function alphanumeric(): Closure
    {
        return fn (string $actual): bool => ctype_alnum($actual);
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function lowercase(): Closure
    {
        return fn (string $actual): bool => $actual === mb_strtolower($actual);
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function uppercase(): Closure
    {
        return fn (string $actual): bool => $actual === mb_strtoupper($actual);
    }

    /**
     * @psalm-return Closure(string):bool
     */
    public static function notEmpty(): Closure
    {
        return fn (string $actual): bool => $actual !== '';
    }
}
