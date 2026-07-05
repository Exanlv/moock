<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer\Extractor;

use Exan\Moock\Analyzer\TokenEmitter;
use Exan\Moock\Analyzer\TokenFilter;

/**
 * Deliberate typo. Namespace is reserved
 */
class Nemaspace
{
    public function __construct(
        private readonly TokenEmitter $tokenEmitter,
        private ?array &$namespace,
    ) {
        $this->tokenEmitter->on(TokenFilter::ofType(T_NAMESPACE), function (array $token) {
            $this->namespace = [$token];

            $capture = $this->tokenEmitter->all(function (string|array $token) {
                $this->namespace[] = $token;
            });

            $end = $this->tokenEmitter->on(
                TokenFilter::eq(';'),
                function () use (&$end, $capture) {
                    /** @var int $end */

                    $this->tokenEmitter->remove($end);
                    $this->tokenEmitter->remove($capture);
                }
            );
        });
    }
}
