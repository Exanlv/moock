<?php

declare(strict_types=1);

namespace Exan\Moock;

use RuntimeException;

class PropertyMocker
{
    public function __construct(
        private readonly MockedClassInterface $mock,
    ) {
    }

    public function forward(mixed ...$props): static
    {
        $accesses = $this->mock->__getAccessedProperties();
        $relevantAccesses = array_slice($accesses, 0 - count($props));

        $combined = array_combine($relevantAccesses, $props);

        foreach ($combined as $propertyName => $value) {
            if ($this->mock->{$propertyName} !== $value) {
                throw new RuntimeException('Unable to determine properties to mock');
            }
        }

        return $this;
    }
}
