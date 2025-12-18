<?php

declare(strict_types=1);

namespace Exan\Moock;

use RuntimeException;

trait MockedClass
{
    private array $replacements = [];

    private array $calls = [];

    public function __replace(string $method, callable $replacement): void
    {
        $this->replacements[$method] = $replacement;
        $this->calls[$method] = 0;
    }

    public function __getCallCount(string $method): int
    {
        return $this->calls[$method] ?? 0;
    }

    public function __call($method, $arguments): mixed
    {
        if (!isset($this->replacements[$method])) {
            throw new RuntimeException('No replacement provided for `' . $method . '`');
        }

        $this->calls[$method]++;

        return $this->replacements[$method](...$arguments);
    }
}
