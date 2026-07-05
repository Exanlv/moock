<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer;

use Closure;

/** @internal */
class TokenFilter
{
    /**
     * @psalm-return Closure(array|string):bool
     */
    public static function eq(string $value): Closure
    {
        return fn (array|string $token) => $token === $value;
    }

    /**
     * @psalm-return Closure(array|string):bool
     */
    public static function ofType(int $type): Closure
    {
        return fn (array|string $token) => is_array($token) && $token[0] === $type;
    }

    /**
     * @psalm-return Closure(array|string):bool
     */
    public static function any(Closure ...$closures): Closure
    {
        return function (array|string $token) use ($closures) {
            foreach ($closures as $closure) {
                if ($closure($token)) {
                    return true;
                }
            }

            return false;
        };
    }
}
