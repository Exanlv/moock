<?php

declare(strict_types=1);

namespace Exan\Moock;

use ReflectionFunction;
use RuntimeException;

class MethodExpectation
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

    public function with(mixed ...$filters): static
    {
        return $this;
    }
}
