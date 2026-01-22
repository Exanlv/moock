<?php

declare(strict_types=1);

namespace Exan\Moock;

use ReflectionFunction;
use RuntimeException;

class MockMethod
{
    private readonly MockedClassInterface $classMock;

    private readonly string $methodName;

    public function __construct(
        private readonly ReflectionFunction $ref,
    ) {
        $classMock = $this->ref->getClosureThis();
        if (!$classMock instanceof MockedClassInterface) {
            throw new RuntimeException('Closure class is not mocked');
        }

        $this->classMock = $classMock;
        $this->methodName = $this->ref->getName();
    }

    public function replace(callable $replacement): void
    {
        $this->classMock->__replace($this->methodName, $replacement);
    }

    public function forceReturn(mixed $returnValue): void
    {
        $this->classMock->__replace($this->methodName, fn () => $returnValue);
    }

    public function forceReturnSequence(array $values): void
    {
        $this->classMock->__replace($this->methodName, function () use (&$values): mixed {
            return array_shift($values);
        });
    }

    /**
     * @param class-string<Throwable> $exception
     */
    public function throwsException(string $exception)
    {
        $this->classMock->__replace($this->methodName, function () use ($exception): never {
            throw new $exception();
        });
    }

    public function should(): Expectation
    {
        return new Expectation($this->methodName, $this->classMock->__getCalls($this->methodName));
    }
}
