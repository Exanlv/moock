<?php

declare(strict_types=1);

namespace Exan\Moock;

use Exan\Moock\Args\Arr;
use Exan\Moock\Dto\MethodCall;
use ReflectionFunction;
use RuntimeException;
use Throwable;

class MockMethod
{
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

    public function allow(mixed ...$filters): static
    {
        $limiter = $this->classMock instanceof MockFnInterface
            ? ['inputs' => Arr::partial($filters)]
            : $filters;

        $this->classMock->__filter($this->methodName, ...$limiter);

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

    public function returns(mixed $returnValue): static
    {
        $this->classMock->__replace($this->methodName, fn (): mixed => $returnValue);

        return $this;
    }

    public function returnsSequence(array $values): static
    {
        $this->classMock->__replace($this->methodName, function () use (&$values): mixed {
            return array_shift($values);
        });

        return $this;
    }

    /**
     * @param class-string<Throwable> $exception
     */
    public function throws(string $exception): static
    {
        $this->classMock->__replace($this->methodName, function () use ($exception): never {
            throw new $exception();
        });

        return $this;
    }

    public function assert(): Expectation
    {
        return new Expectation(
            $this->classMock,
            $this->methodName,
            array_map(
                fn (MethodCall $call) => $call->args,
                $this->classMock->__getCalls($this->methodName)
            )
        );
    }
}
