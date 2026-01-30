<?php

declare(strict_types=1);

namespace Exan\Moock;

use Closure;
use RuntimeException;

class Expectation
{
    public function __construct(
        private readonly string $methodName,
        private readonly array $calls,
        private readonly bool $expectation = true,
    ) {
    }

    public function not(): Expectation
    {
        return new Expectation($this->methodName, $this->calls, !$this->expectation);
    }

    public function with(mixed ...$expectedArg): Expectation
    {
        if (empty($this->calls)) {
            return $this;
        }

        $expectedArg = array_is_list($expectedArg)
            ? $this->convertToArgDictionary($expectedArg)
            : $expectedArg;

        $validCalls = $this->calls;

        foreach ($expectedArg as $name => $valueOrValidator) {
            $validator = $valueOrValidator instanceof Closure
                ? fn ($call): bool => $valueOrValidator($call[$name])
                : fn ($call): bool => $call[$name] === $valueOrValidator;

            $validCalls = array_filter($validCalls, $validator);
        }

        return new Expectation($this->methodName, $validCalls, $this->expectation);
    }

    private function convertToArgDictionary($args): array
    {
        $argKeys = array_keys($this->calls[0]);

        if (count($args) > count($argKeys)) {
            throw new RuntimeException(sprintf(
                'Method %s only has %d parameters, %d expectations given. Note: variadic args are validated as a singular array',
                $this->methodName,
                count($argKeys),
                count($args),
            ));
        }

        while (count($args) < count($argKeys)) {
            array_pop($argKeys);
        }

        return array_combine($argKeys, $args);
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

        $this->phpunitCompatibleAssert($callsCount === $expectedCalls, $message);
    }

    public function toHaveBeenCalled(): void
    {
        $callsCount = $this->callsAmount();

        $message = $this->expectation
            ? sprintf('Method %s should have been called at least once', $this->methodName)
            : sprintf('Method %s should not have been called, but was called %d time(s)', $this->methodName, $callsCount);

        $this->phpunitCompatibleAssert($callsCount > 0, $message);
    }

    public function toHaveBeenCalledOnce(): void
    {
        $this->toHaveBeenCalledTimes(1);
    }

    public function dd(): never
    {
        $data = ['method' => $this->methodName, 'calls' => $this->calls, 'expectation' => $this->expectation];

        if (function_exists('dd')) {
            dd($data);;
        }

        var_dump($this->calls);
        die();
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
