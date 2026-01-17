<?php

declare(strict_types=1);

namespace Exan\Moock;

use RuntimeException;

trait MockedClass
{
    private array $replacements = [];

    private array $calls = [];

    private mixed $spyOn = null;

    public function __replace(string $method, callable $replacement): void
    {
        $this->replacements[$method] = $replacement;
        $this->calls[$method] = 0;
    }

    public function __getCallCount(string $method): int
    {
        return $this->calls[$method] ?? 0;
    }

    public function __setPartial(mixed $spyOn): void
    {
        $this->spyOn = $spyOn;
        $this->calls = [];
    }

    private function __moockFunctionCall($method, $arguments): mixed
    {
        if (!key_exists($method, $this->calls)) {
            $this->calls[$method] = 0;
        }

        $this->calls[$method]++;

        if (!isset($this->replacements[$method])) {
            if ($this->spyOn !== null && method_exists($this->spyOn, $method)) {
                return $this->spyOn->{$method}(...$arguments);
            }

            throw new RuntimeException('No replacement provided for `' . $method . '`');
        }

        return $this->replacements[$method](...$arguments);
    }
}
