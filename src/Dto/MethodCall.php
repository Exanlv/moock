<?php

declare(strict_types=1);

namespace Exan\Moock\Dto;

/** @internal */
readonly class MethodCall
{
    public function __construct(
        public int $methodCallId,
        public array $args,
    ) {
    }
}
