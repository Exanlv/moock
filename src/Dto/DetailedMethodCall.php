<?php

declare(strict_types=1);

namespace Exan\Moock\Dto;

/** @internal */
readonly class DetailedMethodCall
{
    public function __construct(
        public string $objectHash,
        public string $method,
        public MethodCall $call,
    ) {}
}
