<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer;

/**
 * @internal
 *
 * I am not a language dev, please don't judge me too harshly for this poor excuse of a parser :)
 *
 * Valid PHP can be assumed, as the files have gone through reflection already prior to reaching this stage.
 */
class Utilize
{
    /**
     * @param ?string $declaringNamespace
     * @param array<string, string> $uses
     */
    public function __construct(
        private readonly ?string $declaringNamespace,
        private readonly array $uses,
    ) {}

    public function fullyQuantify(string $className): string
    {
        if (isset($this->uses[$className])) {
            return $this->format($this->uses[$className]);
        }

        return $this->format(($this->declaringNamespace ?? '') . '\\' . $className);
    }

    private function format(string $fullyQualifiedClass): string
    {
        return '\\' . trim($fullyQualifiedClass, '\\');
    }

    /**
     * @param string $declaringNamespace
     * @param array<int, array<int, string|array{0:int, 1:string, 2:int}>> $uses
     */
    public static function fromTokens(?string $declaringNamespace, array $uses): self
    {
        $convertedUses = array_merge(...array_map(static::parseLine(...), $uses));

        return new self($declaringNamespace, array_merge(...$convertedUses));
    }

    /**
     * @param array<int, string|array{0:int, 1:string, 2:int}> $line
     *
     * @return (mixed|string)[][]
     *
     * @psalm-return array<int<0, max>, array<string, mixed|string>>
     */
    private static function parseLine($line): array
    {
        array_shift($line); // use
        array_shift($line); // (whitespace)
        array_pop($line); // ;

        return array_map(static::parseIndividualUse(...), static::individualUses($line));
    }

    /**
     * @param array<int, string|array{0:int, 1:string, 2:int}> $tokens
     *
     * @return ((int|string)[]|string)[][]
     *
     * @psalm-return array<int<0, max>, list<array{0: int, 1: string, 2: int}|string>>
     */
    private static function individualUses($tokens): array
    {
        $individual = [[]];
        $blockLevel = 0;
        $i = 0;

        foreach ($tokens as $token) {
            if ($token === '{') {
                $blockLevel++;
            }

            if ($token === '}') {
                $blockLevel--;
            }

            if ($blockLevel === 0 && $token === ',') {
                $individual[] = [];
                $i++;
                continue;
            }

            $individual[$i][] = $token;
        }

        return $individual;
    }

    /**
     * @return (mixed|string)[]
     *
     * @psalm-return array<string, mixed|string>
     */
    private static function parseIndividualUse(array $use): array
    {
        while (is_array($use[0]) && $use[0][0] === T_WHITESPACE) {
            array_shift($use);
        }

        if (count($use) === 1) {
            // use Some\Potentially\Namespaced\Class
            $split = explode('\\', $use[0][1]);
            return [array_pop($split) => $use[0][1]];
        }

        if (count($use) === 5 && is_array($use[2]) && $use[2][1] === 'as') {
            // use Some\Class as Alias

            /** @var array{0: int, 1: string, 2: int} */
            $trueImport = array_shift($use);
            /** @var array{0: int, 1: string, 2: int} */
            $alias = array_pop($use);

            return [
                $alias[1] => $trueImport[1],
            ];
        }

        // use Some\Potentially\Namespaced\{Class, Or\Other\Class}
        return static::parseGroupUse($use);
    }

    /**
     * @param array<int, string|array{0:int, 1:string, 2:int}> $tokens
     *
     * @return string[]
     *
     * @psalm-return array<string, string>
     */
    private static function parseGroupUse(array $tokens): array
    {
        $namespacePrefix = '';
        while (count($tokens) && $tokens[0] !== '{') {
            $token = array_shift($tokens);

            $namespacePrefix .= (is_array($token) ? $token[1] : $token);
        }

        array_shift($tokens); // {
        array_pop($tokens); // }

        $imports = static::splitGroupUse($tokens);

        return array_merge(...array_map(function (array $tokens) use ($namespacePrefix) {
            while (count($tokens) && is_array($tokens[0]) && $tokens[0][0] === T_WHITESPACE) {
                array_shift($tokens);
            }

            $fullImport = $namespacePrefix . $tokens[0][1];

            if (count($tokens) < 3) { // Not aliased, possibly followed by whitespace
                $split = explode('\\', $fullImport);

                $alias = array_pop($split);
            } else { // Aliased
                $alias = $tokens[4][1];
            }

            return [$alias => $fullImport];
        }, $imports));
    }

    /**
     * @param array<int, string|array{0:int, 1:string, 2:int}> $tokens
     *
     * @return ((int|string)[]|string)[][]
     *
     * @psalm-return array<int<0, max>, list<array{0: int, 1: string, 2: int}|string>>
     */
    private static function splitGroupUse(array $tokens): array
    {
        $imports = [[]];
        $i = 0;

        foreach ($tokens as $token) {
            if ($token === '}') {
                break;
            }

            if ($token === ',') {
                $imports[] = [];
                $i++;
                continue;
            }

            $imports[$i][] = $token;
        }

        return $imports;
    }
}
