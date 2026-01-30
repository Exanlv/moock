<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;

class Arr
{
    public static function count(int $expectedCount): Closure
    {
        return function (array $actual) use ($expectedCount): bool {
            return count($actual) === $expectedCount;
        };
    }

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
                    if (!$validator($actualValue, $expectedValue)) {
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
}
