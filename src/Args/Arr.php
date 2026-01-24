<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Arr
{
    public static function withCount(int $expectedCount): Closure
    {
        return function (array $actual) use ($expectedCount): bool {
            return count($actual) === $expectedCount;
        };
    }

    // public static function partialMatch(array $expectation): Closure
    // {
    //     return function (array $actual) use ($expectation): bool {
    //         return false;
    //     };
    // }
}
