<?php

declare(strict_types=1);

namespace Exan\Moock\Expector;

use Exan\Moock\Dto\DetailedMethodCall;
use Exan\Moock\FiltersMethodArgs;
use Exan\Moock\MockedClassInterface;
use ReflectionFunction;
use RuntimeException;

/** @internal */
class Expectation
{
    use FiltersMethodArgs;

    public private(set) array $with;

    private MockedClassInterface $mock;

    public function __construct(public readonly ReflectionFunction $ref)
    {
        $classMock = $this->ref->getClosureThis();
        if (!$classMock instanceof MockedClassInterface) {
            throw new RuntimeException('Closures\' parent object is not mocked');
        }

        $this->mock = $classMock;
    }

    public function with(mixed ...$with): void
    {
        $this->with = $with;
    }

    public function matches(DetailedMethodCall $call): bool
    {
        if ($call->objectHash !== spl_object_hash($this->mock)) {
            return false;
        }

        if ($call->method !== $this->ref->getName()) {
            return false;
        }

        if (isset($this->with) && empty(
            $this->filterArgs([$call->call->args], $this->with)
        )) {
            return false;
        }

        return true;
    }
}
