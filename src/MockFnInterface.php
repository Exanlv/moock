<?php

declare(strict_types=1);

namespace Exan\Moock;

interface MockFnInterface
{
    public function mockFn(mixed ...$inputs): mixed;
}
