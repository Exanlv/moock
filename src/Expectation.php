<?php

declare(strict_types=1);

namespace Exan\Moock;

use Closure;
use RuntimeException;

class Expectation
{
    use FiltersMethodArgs;

    public function __construct(
        private readonly string $methodName,
        private readonly array $calls,
        private readonly bool $expectation = true,
    ) {}

    public function not(): Expectation
    {
        return new Expectation($this->methodName, $this->calls, !$this->expectation);
    }

    public function with(mixed ...$expectedArg): Expectation
    {
        if (empty($this->calls)) {
            return $this;
        }

        $filteredCalls = $this->filterArgs($this->calls, $expectedArg);

        return new Expectation($this->methodName, $filteredCalls, $this->expectation);
    }

    private function callsAmount(): int
    {
        return count($this->calls);
    }

    public function toHaveBeenCalledTimes(int $expectedCalls): void
    {
        $callsCount = $this->callsAmount();

        $message = $this->expectation
            ? sprintf('Method %s should have been called %d time(s), but was called %d times', $this->methodName, $expectedCalls, $callsCount)
            : sprintf('Method %s should not have been called %d time(s)', $this->methodName, $expectedCalls);

        $this->testingToolCompatibleAssert($callsCount === $expectedCalls, $message);
    }

    public function toHaveBeenCalled(): void
    {
        $callsCount = $this->callsAmount();

        $message = $this->expectation
            ? sprintf('Method %s should have been called at least once', $this->methodName)
            : sprintf('Method %s should not have been called, but was called %d time(s)', $this->methodName, $callsCount);

        $this->testingToolCompatibleAssert($callsCount > 0, $message);
    }

    public function toHaveBeenCalledOnce(): void
    {
        $this->toHaveBeenCalledTimes(1);
    }

    public function dd(): never
    {
        $data = ['method' => $this->methodName, 'calls' => $this->calls, 'expectation' => $this->expectation];

        if (function_exists('dd')) {
            dd($data);
        }

        var_dump($this->calls);
        die();
    }

    private function testingToolCompatibleAssert(bool $condition, string $message): void
    {
        if (class_exists("\PHPUnit\Framework\Assert")) {
            // @see https://packagist.org/packages/phpunit/phpunit
            '\PHPUnit\Framework\Assert'::assertEquals($this->expectation, $condition, $message);
        } elseif (class_exists('\Tester\Assert')) {
            // @see https://packagist.org/packages/nette/tester
            '\Tester\Assert'::same($this->expectation, $condition, $message);
        } else {
            assert($this->expectation === $condition, $message);
        }
    }
}
