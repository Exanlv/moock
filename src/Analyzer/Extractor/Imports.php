<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer\Extractor;

use Exan\Moock\Analyzer\TokenEmitter;
use Exan\Moock\Analyzer\TokenFilter;

/** @internal */
class Imports
{
    private int $blockLevel = 0;

    public function __construct(
        public readonly TokenEmitter $tokenEmitter,
        public array &$imports,
    ) {
        $tokenEmitter->on(
            TokenFilter::eq('{'),
            function () use (&$blockLevel): void {
                $this->blockLevel++;
            }
        );

        $tokenEmitter->on(
            TokenFilter::eq('}'),
            function () use (&$blockLevel): void {
                $this->blockLevel--;
            }
        );

        $tokenEmitter->on(
            TokenFilter::ofType(T_USE),
            $this->handleUse(...),
        );
    }

    private function handleUse(array $token): void
    {
        if ($this->blockLevel > 0) {
            return;
        }

        $statement = [$token];

        $capture = $this->tokenEmitter->all(function (string|array $token) use (&$statement): void {
            $statement[] = $token;
        });

        $end = $this->tokenEmitter->on(
            TokenFilter::eq(';'),
            function () use (&$end, $capture, &$statement): void {
                /** @var int $end */

                $this->imports[] = $statement;

                $this->tokenEmitter->remove($end);
                $this->tokenEmitter->remove($capture);
            }
        );
    }
}
