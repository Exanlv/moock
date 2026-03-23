<?php

declare(strict_types=1);

namespace Exan\Moock;

use ReflectionClass;
use RuntimeException;

/**
 * @internal
 */
trait MockedClass
{
    use FiltersMethodArgs;

    private array $replacements = [];

    private array $calls = [];
    private array $filters = [];

    private mixed $spyOn = null;

    private array $forwardedMagicProperties = [];

    public function __replace(string $method, callable $replacement): void
    {
        $this->replacements[$method] = $replacement;
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
        $this->spyOn = $spyOn;
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

        if (!isset($this->replacements[$method])) {
            if ($this->spyOn !== null && method_exists($this->spyOn, $method)) {
                return $this->spyOn->{$method}(...$args);
            }

            return null;
        }



        return $this->replacements[$method](...$args);
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

    public function __forwardProp(string $property)
    {
        if (property_exists($this, $property)) {
            $this->{$property} = &$this->spyOn->$property;
            return;
        }

        $this->forwardedMagicProperties[] = $property;
    }

    public function __moockPropertyGet(string $property): mixed
    {
        return null;
    }

    public function __mockPropertySet(string $property, mixed $value): mixed
    {
        return $value;
    }
}
