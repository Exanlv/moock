<?php

declare(strict_types=1);

namespace Exan\Moock;

class Expectation
{
    public function __construct(
        private readonly string $methodName,
        private readonly array $calls,
        private readonly bool $expectation = true,
    ) {}

    public function not(): Expectation
    {
        return new Expectation($this->methodName, $this->calls, !$this->expectation);
    }

    private function callsAmount(): int
    {
        return count($this->calls);
    }

    public function haveBeenCalledTimes(int $expectedCalls): void
    {
        $callsCount = $this->callsAmount();

        $message = $this->expectation
            ? sprintf('Method %s should have been called %d time(s), but was called %d times', $this->methodName, $expectedCalls, $callsCount)
            : sprintf('Method %s should not have been called %d time(s)', $this->methodName, $expectedCalls);

        $this->phpunitCompatibleAssert($callsCount === $expectedCalls, $message);
    }

    public function haveBeenCalled(): void
    {
        $callsCount = $this->callsAmount();

        $message = $this->expectation
            ? sprintf('Method %s should have been called at least once', $this->methodName)
            : sprintf('Method %s should not have been called, but was called %d time(s)', $this->methodName);

        $this->phpunitCompatibleAssert($callsCount > 0, $message);
    }

    public function haveBeenCalledOnce(): void
    {
        $this->haveBeenCalledTimes(1);
    }

    private function phpunitCompatibleAssert(bool $condition, string $message): void
    {
        if (class_exists("\PHPUnit\Framework\Assert")) {
            \PHPUnit\Framework\Assert::assertEquals($this->expectation, $condition, $message);
        } else {
            assert($condition, $message);
        }
    }
}
