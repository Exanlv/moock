<?php

declare(strict_types=1);

namespace Exan\Moock;

interface MockedClassInterface
{
    public function __setQuine(string $code): void;
    public function __replace(string $method, callable $replacement): void;
    public function __filter(string $method, mixed ...$filters): void;
    public function __getCalls(string $method): array;
    public function __makePartial(mixed $spyOn): void;

    public function __moockPropertyGet(string $property): mixed;
    public function __mockPropertySet(string $property, mixed $value): mixed;
    public function __getAccessedProperties(): array;
}
