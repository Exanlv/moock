<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer;

use Exan\Moock\Analyzer\Dto\Yanked;
use Exan\Moock\Analyzer\Extractor\Imports;
use Exan\Moock\Analyzer\Extractor\MethodArgs;
use Exan\Moock\Analyzer\Extractor\Nemaspace;

/**
 * @internal
 *
 * I am not a language dev, please don't judge me too harshly for this poor excuse of a parser :)
 *
 * Valid PHP can be assumed, as the files have gone through reflection already prior to reaching this stage.
 */
class Yanker
{
    public static function fetch(
        string $contents,
        array $method,
    ): Yanked {
        $tokenEmitter = new TokenEmitter();
        $tokens = token_get_all($contents, TOKEN_PARSE);

        $imports = [];
        $namespace = null;
        $methodArgs = null;

        new Imports($tokenEmitter, $imports);
        new Nemaspace($tokenEmitter, $namespace);
        new MethodArgs($tokenEmitter, $method, $methodArgs);

        $tokenEmitter->emit($tokens);

        return new Yanked($namespace, $imports, $methodArgs);
    }
}
