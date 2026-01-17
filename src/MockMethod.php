<?php

declare(strict_types=1);

namespace Exan\Moock;

use ReflectionFunction;
use RuntimeException;

class MockMethod
{
    private readonly MockedClassInterface $classMock;

    public function __construct(
        private readonly ReflectionFunction $ref
    ) {
        $classMock = $this->ref->getClosureThis();
        if (!$classMock instanceof MockedClassInterface) {
            throw new RuntimeException('Closure class is not mocked');
        }

        $this->classMock = $classMock;
    }

    public function replace(callable $replacement): void
    {
        $this->classMock->__replace($this->ref->getName(), $replacement);
    }

    public function forceReturn(mixed $returnValue): void
    {
        $this->classMock->__replace($this->ref->getName(), fn () => $returnValue);
    }

    public function forceReturnSequence(array $values): void
    {
        $this->classMock->__replace($this->ref->getName(), function () use (&$values): mixed {
            return array_shift($values);
        });
    }

    /**
     * @param class-string<Throwable> $exception
     */
    public function throwsException(string $exception)
    {
        $this->classMock->__replace($this->ref->getName(), function () use ($exception): never {
            throw new $exception();
        });
    }

    public function calls(): int
    {
        return $this->classMock->__getCallCount($this->ref->getName());
    }

    public function shouldNotHaveBeenCalled(): void
    {
        $this->shouldHaveBeenCalledTimes(0);
    }

    public function shouldHaveBeenCalledOnce(): void
    {
        $this->shouldHaveBeenCalledTimes(1);
    }

    public function shouldHaveBeenCalledTimes(int $expectedCalls): void
    {
        $calls = $this->calls();
        
        assert(
            $calls === $expectedCalls,
            sprintf('Method should have been called %d time(s), but was called %d times', $expectedCalls, $calls)
        );
    }

    public function shouldNotHaveBeenCalledTimes(int $notExpectedCalls): void
    {
        $calls = $this->calls();

        assert(
            $calls !== $notExpectedCalls,
            sprintf('Method should not have been called %d time(s)', $notExpectedCalls)
        );
    }
}
