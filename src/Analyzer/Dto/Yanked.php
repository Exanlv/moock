<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer\Dto;

readonly class Yanked
{
    public function __construct(
        public ?array $namespace,
        public array $uses,
        public ?array $args,
    ) {}
}
