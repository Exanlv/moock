<?php

declare(strict_types=1);

namespace Exan\Moock;

class PropertyMocker
{
    public function __construct(
        private readonly MockedClassInterface $object,
    ) {
    }
}
