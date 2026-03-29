<?php

declare(strict_types=1);

namespace Exan\Moock;

interface MockedClassInterface
{
    /** @internal */
    public function __setQuine(string $code): void;
    /** @internal */
    public function __replace(string $method, callable $replacement): void;
    /** @internal */
    public function __filter(string $method, mixed ...$filters): void;
    /** @internal */
    public function __getCalls(string $method): array;
    /** @internal */
    public function __makePartial(mixed $spyOn): void;

    /** @internal */
    public function __moockPropertyGet(string $property): mixed;
    /** @internal */
    public function __mockPropertySet(string $property, mixed $value): mixed;
    /** @internal */
    public function __getAccessedProperties(): array;
}
