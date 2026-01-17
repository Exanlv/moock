<?php

declare(strict_types=1);

namespace Exan\Moock;

interface MockedClassInterface
{
    public function __replace(string $method, callable $replacement): void;
    public function __getCallCount(string $method): int;
    public function __setPartial(mixed $spyOn): void;
}
