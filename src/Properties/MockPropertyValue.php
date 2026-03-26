<?php

declare(strict_types=1);

namespace Exan\Moock\Properties;

readonly class MockPropertyValue
{
    public function __construct(
        public mixed $hasValue,
        public mixed $value,
    ) {}
}
