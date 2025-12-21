<?php

declare(strict_types=1);

namespace Exan\Moock;

use Closure;
use ReflectionFunction;

class Mock
{
    /**
     * Returns a Mock for a singular given interface
     *
     * @template T
     * @param class-string<T> $interface
     * @return T&MockedClassInterface
     */
    public static function interface($interface): mixed
    {
        return self::interfaces($interface);
    }

    /**
     * Returns a singular Mock for all given interfaces.
     *
     * Manually type the result with @var for proper IDE support.
     *
     * @param class-string $interfaces
     */
    public static function interfaces(...$interfaces): mixed
    {
        $classBuilder = new MockClassBuilder(null, $interfaces);

        return eval($classBuilder->getCode());
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return T&MockedClassInterface
     */
    public static function class($class): mixed
    {
        $classBuilder = new MockClassBuilder($class);

        return eval($classBuilder->getCode());
    }

    public static function method(Closure $arg)
    {
        $ref = new ReflectionFunction($arg);

        return new MockMethod($ref);
    }
}
