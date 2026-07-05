<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer\Extractor;

use Exan\Moock\Analyzer\TokenEmitter;
use Exan\Moock\Analyzer\TokenFilter;

/**
 * Deliberate typo. Namespace is reserved
 *
 * @internal
 */
class Nemaspace
{
    public function __construct(
        readonly TokenEmitter $tokenEmitter,
        private ?array &$namespace,
    ) {
        $this->tokenEmitter->on(TokenFilter::ofType(T_NAMESPACE), function (array $token): void {
            $this->namespace = [$token];

            $capture = $this->tokenEmitter->all(function (string|array $token): void {
                $this->namespace[] = $token;
            });

            $this->tokenEmitter->once(TokenFilter::eq(';'), fn () => $this->tokenEmitter->remove($capture));
        });
    }
}
