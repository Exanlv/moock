<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Arr
{
    /**
     * @psalm-return Closure(array):bool
     */
    public static function count(int $expectedCount): Closure
    {
        return fn (iterable $actual): bool => count($actual) === $expectedCount;
    }

    /**
     * @psalm-return Closure(array):bool
     */
    public static function partial(array $expectation): Closure
    {
        $validator = function (array $actual, array $expectation) use (&$validator): bool {
            foreach ($expectation as $key => $expectedValue) {
                if (!array_key_exists($key, $actual)) {
                    return false;
                }

                $actualValue = $actual[$key];

                if ($expectedValue instanceof Closure) {
                    if ($expectedValue($actualValue) !== true) {
                        return false;
                    }
                    continue;
                }

                if (is_array($expectedValue)) {
                    if (!is_array($actualValue) || !$validator($actualValue, $expectedValue)) {
                        return false;
                    }
                    continue;
                }

                if ($actualValue !== $expectedValue) {
                    return false;
                }
            }

            return true;
        };

        return fn (array $actual) => $validator($actual, $expectation);
    }

    /**
     * @psalm-return Closure(array):bool
     */
    public static function all(Closure $match): Closure
    {
        return function (array $actual) use ($match): bool {
            foreach ($actual as $item) {
                if (!$match($item)) {
                    return false;
                }
            }

            return true;
        };
    }

    /**
     * @psalm-return Closure(array):bool
     */
    public static function any(Closure $match): Closure
    {
        return function (array $actual) use ($match): bool {
            foreach ($actual as $item) {
                if ($match($item)) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * @psalm-return Closure(array):bool
     */
    public static function none(Closure $match): Closure
    {
        return function (array $actual) use ($match): bool {
            foreach ($actual as $item) {
                if ($match($item)) {
                    return false;
                }
            }

            return true;
        };
    }

    /**
     * @psalm-return Closure(array):bool
     */
    public static function contains(mixed $expected): Closure
    {
        return fn (array $actual): bool => in_array($expected, $actual, true);
    }

    /**
     * @psalm-return Closure(array):bool
     */
    public static function keys(array $expectedKeys): Closure
    {
        return fn (array $actual): bool => count(array_diff($expectedKeys, array_keys($actual))) === 0;
    }

    /**
     * @psalm-return Closure(array):bool
     */
    public static function empty(): Closure
    {
        return fn (array $actual): bool => empty($actual);
    }

    /**
     * @psalm-return Closure(array):bool
     */
    public static function notEmpty(): Closure
    {
        return fn (array $actual): bool => !empty($actual);
    }

    /**
     * @psalm-return Closure(array):bool
     */
    public static function indexed(): Closure
    {
        return fn (array $actual): bool => array_keys($actual) === range(0, count($actual) - 1);
    }
}
