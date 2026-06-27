<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer;

use Closure;
use Exan\Moock\Analyzer\Dto\Yanked;

class Yanker
{
    public static function fetch(
        string $contents,
        array $methods = [],
    ): Yanked {
        $tokens = token_get_all($contents, TOKEN_PARSE);

        $methods = array_unique($methods);

        $uses = [];
        $namespace = null;
        $args = [];

        $blockLevel = 0;

        /**
         * @var array{
         *     0: Closure(string|array): bool,
         *     1: Closure(array): void,
         *     2: array<int, array|string>
         * }
         */
        $captures = [];

        /**
         * @var callable(Closure(string|array $token): bool, Closure(array $tokens): void): void
         */
        $captureUntil = function (Closure $end, Closure $then) use (&$captures): void {
            $captures[] = [$end, $then, []];
        };

        $handleCaptures = function (string|array $token) use (&$captures) {
            foreach ($captures as $i => &$capture) {
                $capture[2][] = $token;

                if ($capture[0]($token)) {
                    $capture[1]($capture[2]);
                    unset($captures[$i]);
                }
            }
        };

        $currentClass = [];
        $currentMethodArgs = null;
        $wasWhitespace = false;
        foreach ($tokens as $token) {
            if (static::isType($token, T_COMMENT)) {
                continue;
            }

            $isWhitespace = static::isType($token, T_WHITESPACE);
            if ($isWhitespace) {
                if ($wasWhitespace) {
                    continue;
                }

                $wasWhitespace = true;
            } else {
                $wasWhitespace = false;
            }

            if ($token === '{') {
                $blockLevel++;
            }

            if ($token === '}') {
                $blockLevel--;

                $lastClass = static::lastItemInArray($currentClass) ?? -1;

                if ($blockLevel < $lastClass) {
                    array_pop($currentClass);
                }
            }

            if ($blockLevel === 0 && static::isType($token, T_USE)) {
                $captureUntil(fn (string|array $token) => $token === ';', function (array $tokens) use (&$uses) {
                    $uses[] = $tokens;
                });
            }

            if (static::isType($token, T_NAMESPACE)) {
                $captureUntil(fn (string|array $token) => $token === ';', function (array $tokens) use (&$namespace) {
                    $namespace = $tokens;
                });
            }

            if (static::isType($token, T_CLASS)) {
                $captureUntil(fn(string|array $token) => static::isType($token, T_STRING), function (array $tokens) use (&$currentClass, $blockLevel) {
                    $nameToken = array_pop($tokens);
                    $currentClass[$nameToken[1]] = $blockLevel + 1;
                });
            }

            if (static::isType($token, T_FUNCTION) && count($currentClass) > 0) {
                $captureUntil(fn (string|array $token) => static::isType($token, T_STRING),
                function (array $fnTokens) use ($captureUntil, $currentClass, &$currentMethodArgs, &$args, $methods) {
                        $nameToken = array_pop($fnTokens);
                        $currentMethodArgs = array_key_last($currentClass) . '.' . $nameToken[1];

                        $captureUntil(
                            fn (string|array $token) => $token === '{' || $token === ';',
                            function (array $tokens) use (&$currentMethodArgs, &$args, $fnTokens, $methods) {
                                $tokens = [
                                    ...$fnTokens,
                                    ...$tokens,
                                ];

                                if (in_array($currentMethodArgs, $methods)) {
                                    $args[$currentMethodArgs] = $tokens;
                                }

                                $currentMethodArgs = null;
                            }
                        );
                    }
                );
            }

            $handleCaptures($token);
        }

        return new Yanked($namespace, $uses, $args);
    }

    private static function isType(string|array $token, int $type): bool
    {
        return is_array($token) && $token[0] === $type;
    }

    private static function lastItemInArray(array $items): mixed
    {
        if ($items === []) {
            return null;
        }

        return end($items);
    }
}
