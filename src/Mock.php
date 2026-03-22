<?php

declare(strict_types=1);

namespace Exan\Moock;

use Closure;
use Exan\Moock\Class\Mocker as ClassMocker;
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
    public static function interface(string $interface): mixed
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
    public static function interfaces(string ...$interfaces): mixed
    {
        $mocker = new ClassMocker();

        foreach ($interfaces as $interface) {
            $mocker->addInterface($interface);
        }

        return eval($mocker->getCode());
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return T&MockedClassInterface
     */
    public static function class(string $class): mixed
    {
        if (str_contains($class, '@anonymous')) {
            return self::anonymousClass($class);
        }

        $mocker = new ClassMocker();

        return eval($mocker->extends($class)->getCode());
    }

    private static function anonymousClass(string $class): mixed
    {
        $implements = class_implements($class);

        $mocker = new ClassMocker();

        foreach ($implements as $interface) {
            $mocker->addInterface($interface);
        }

        return eval($mocker->extends($class)->getCode());
    }

    public static function method(Closure $arg)
    {
        $ref = new ReflectionFunction($arg);

        return new MockMethod($ref);
    }

    public static function partial(MockedClassInterface $mock, mixed $spyOn): void
    {
        $mock->__setPartial($spyOn);
    }

    public static function properties(MockedClassInterface $mock): PropertyMocker
    {
        return new PropertyMocker($mock);
    }
}
