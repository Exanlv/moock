<?php

declare(strict_types=1);

namespace Exan\Moock;

use Exan\Moock\Properties\MockPropertyValue;
use ReflectionClass;
use RuntimeException;

/**
 * @internal
 */
trait MockedClass
{
    use FiltersMethodArgs;

    private array $propertyAccesses = [];

    /** @var string[] */
    private array $forwardedProperties = [];

    private array $propertyReplacements = [];
    private array $methodReplacements = [];

    private array $calls = [];
    private array $filters = [];

    private mixed $real = null;

    public private(set) string $quine;

    public function __setQuine(string $code): void
    {
        $this->quine = $code;
    }

    public function __replace(string $method, callable $replacement): void
    {
        $this->methodReplacements[$method] = $replacement;
        $this->calls[$method] = [];
    }

    public function __filter(string $method, mixed ...$filters): void
    {
        $this->filters[$method] = $filters;
    }

    public function __getCalls(string $method): array
    {
        return $this->calls[$method] ?? [];
    }

    public function __setPartial(mixed $spyOn): void
    {
        $this->real = $spyOn;
        $this->calls = [];
    }

    private function __moockFunctionCall(string $method, array $args): mixed
    {
        if (key_exists($method, $this->filters) && $this->callFailsFilter($method, $args)) {
            throw new RuntimeException(sprintf(
                'Method %s called with args that do not pass its set filters. Called with: %s',
                $method,
                print_r($args, true),
            ));
        }

        if (!key_exists($method, $this->calls)) {
            $this->calls[$method] = [];
        }

        $this->calls[$method][] = $args;

        $args = array_values($args);

        if ($this->hasSpread($method)) {
            $lastArg = array_pop($args);

            $args = [
                ...$args,
                ...$lastArg,
            ];
        }

        if (!isset($this->methodReplacements[$method])) {
            if ($this->real !== null && method_exists($this->real, $method)) {
                return $this->real->{$method}(...$args);
            }

            return null;
        }

        return $this->methodReplacements[$method](...$args);
    }

    private function callFailsFilter(string $method, array $args): bool
    {
        return empty($this->filterArgs([$args], $this->filters[$method]));
    }

    private function formatCalls(string $method, array $argumentNames, array $arguments): array
    {
        if (!$this->hasSpread($method)) {
            return array_combine($argumentNames, $arguments);
        }

        $formatted = [];

        while (count($argumentNames) > 1) {
            $formatted[array_shift($argumentNames)] = array_shift($arguments);
        }

        $formatted[array_shift($argumentNames)] = array_values($arguments);

        return $formatted;
    }

    private function hasSpread(string $method): bool
    {
        $ref = new ReflectionClass($this);
        $method = $ref->getMethod($method);
        $args = $method->getParameters();

        if (empty($args)) {
            return false;
        }

        return $args[array_key_last($args)]->isVariadic();
    }

    public function __getAccessedProperties(): array
    {
        return $this->propertyAccesses;
    }

    public function __moockPropertyGet(string $property): MockPropertyValue
    {
        $this->propertyAccesses[] = $property;

        if (isset($this->real)) {
            return new MockPropertyValue(true, $this->real->{$property});
        }

        return new MockPropertyValue(false, null);
    }

    public function __mockPropertySet(string $property, mixed $value): mixed
    {
        return $value;
    }

    public function __get($property)
    {
        $value = $this->__moockPropertyGet($property);

        if ($value->hasValue) {
            return $value->value;
        }

        $parentClass = get_parent_class($this);
        if ($parentClass !== false && (new ReflectionClass($parentClass))->hasMethod('__get')) {
            return parent::__get($property);
        }
    }
}
