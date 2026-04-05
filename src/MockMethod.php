<?php

declare(strict_types=1);

namespace Exan\Moock;

use ReflectionFunction;
use RuntimeException;

class MockMethod
{
    private static int $methodCallId = 0;

    private readonly MockedClassInterface $classMock;

    private readonly string $methodName;

    public function __construct(
        private readonly ReflectionFunction $ref,
    ) {
        $classMock = $this->ref->getClosureThis();
        if (!$classMock instanceof MockedClassInterface) {
            throw new RuntimeException('Closures\' parent object is not mocked');
        }

        $this->classMock = $classMock;
        $this->methodName = $this->ref->getName();
    }

    public function filter(mixed ...$filters): static
    {
        $this->classMock->__filter($this->methodName, ...$filters);

        return $this;
    }

    public function replace(callable $replacement): static
    {
        $this->classMock->__replace($this->methodName, $replacement);

        return $this;
    }

    public function void(): static
    {
        $this->classMock->__replace($this->methodName, function (): void {});

        return $this;
    }

    public function forceReturn(mixed $returnValue): static
    {
        $this->classMock->__replace($this->methodName, fn () => $returnValue);

        return $this;
    }

    public function forceReturnSequence(array $values): static
    {
        $this->classMock->__replace($this->methodName, function () use (&$values): mixed {
            return array_shift($values);
        });

        return $this;
    }

    /**
     * @param class-string<Throwable> $exception
     */
    public function throwsException(string $exception): static
    {
        $this->classMock->__replace($this->methodName, function () use ($exception): never {
            throw new $exception();
        });

        return $this;
    }

    public function expect(): Expectation
    {
        return new Expectation(
            $this->methodName,
            array_map(
                fn (array $call) => $call['args'],
                $this->classMock->__getCalls($this->methodName)
            )
        );
    }

    public static function getMethodCallId(): int
    {
        return ++static::$methodCallId;
    }
}
