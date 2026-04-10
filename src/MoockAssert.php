<?php

declare(strict_types=1);

namespace Exan\Moock;

use Closure;

class MoockAssert
{
    /**
     * @var null|(Closure(bool $condition, bool $expectation, string $description): void)
     */
    private static ?Closure $assert = null;

    /**
     * @param (Closure(bool $condition, bool $expectation, string $description): void) $assert
     */
    public static function useAssert(?Closure $assert): void
    {
        self::$assert = $assert;
    }

    public static function assert(bool $condition, bool $expected, string $message): void
    {
        if (self::$assert !== null) {
            (self::$assert)($expected, $condition, $message);
            return;
        }

        assert($condition === $expected, $message);
    }
}
