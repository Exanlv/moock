<?php

declare(strict_types=1);

namespace Exan\Moock;

interface MockedClassInterface
{
    public function __replace(string $method, callable $replacement): void;
    public function __getCalls(string $method): array;
    public function __setPartial(mixed $spyOn): void;
}
