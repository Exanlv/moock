<?php

declare(strict_types=1);

namespace Exan\Moock;

/**
 * @internal
 */
class Extractor
{
    /**
     * @param array<int, string|array{0:int, 1:string, 2:int}> $tokens
     * @return array<int, string|array{0:int, 1:string, 2:int}>
     */
    public static function lines(array $tokens, int $start, int $end): array
    {
        $capture = false;
        $captured = [];

        foreach ($tokens as $token) {
            if (!$capture && is_array($token)) {
                $isWithinLines = $token[2] >= $start && $token[2] <= $end;

                if (!$capture && $isWithinLines) {
                    $capture = true;
                } elseif ($capture && !$isWithinLines) {
                    break;
                }
            }

            if ($capture) {
                $captured[] = $token;
            }
        }

        return $captured;
    }

    /**
     * @param array<int, string|array{0:int, 1:string, 2:int}> $tokens
     * @return array<int, string|array{0:int, 1:string, 2:int}>
     */
    public static function function(array $tokens, string $functionName): array
    {
        $capture = false;
        $captured = [];

        foreach ($tokens as $i => $token) {
            if (
                is_array($token)
                && $token[0] === T_FUNCTION
                && is_array($tokens[$i + 2])
                && $tokens[$i + 2][0] === T_STRING
            ) {
                $isRequestedFunction = $tokens[$i + 2][1] === $functionName;

                if (!$capture && $isRequestedFunction) {
                    $capture = true;
                } elseif ($capture && !$isRequestedFunction) {
                    break;
                }
            }

            if ($capture) {
                $captured[] = $token;
            }
        }

        while(
            count($captured)
            && is_array($captured[count($captured) - 1])
            && in_array(
                $captured[count($captured) - 1][0],
                [T_WHITESPACE, T_PUBLIC, T_PROTECTED, T_PRIVATE]
            )
        ) {
            array_pop($captured);
        }

        return $captured;
    }

    /**
     * @param array<int, string|array{0:int, 1:string, 2:int}> $tokens
     * @return array<int, string|array{0:int, 1:string, 2:int}>
     */
    public static function arg(array $tokens, string $argName): array
    {
        $argName = '$' . trim($argName, '$');
        $capture = false;
        $captured = [];

        $trimToLast = ')';

        foreach ($tokens as $token) {
            if (is_array($token)
                && $token[0] === T_VARIABLE
            ) {
                $isRequestedArg = $token[1] === $argName;

                if (!$capture && $isRequestedArg) {
                    $capture = true;
                } elseif ($capture && !$isRequestedArg) {
                    $trimToLast = ',';
                    break;
                }
            }

            if ($token === ':' || $token === '{') {
                break;
            }

            if ($capture) {
                $captured[] = $token;
            }
        }

        while(
            count($captured)
            && array_pop($captured) !== $trimToLast
        ) {
        }

        return $captured;
    }
}
