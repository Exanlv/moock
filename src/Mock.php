<?php

declare(strict_types=1);

namespace Exan\Moock;

use Closure;
use Exan\Moock\Class\Mocker as ClassMocker;
use Exan\Moock\Expector\Expectation;
use Exan\Moock\Expector\MockExpector;
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

        return static::codeToMock($mocker->getCode());
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

        return static::codeToMock($mocker->extends($class)->getCode());
    }

    private static function anonymousClass(string $class): mixed
    {
        $implements = class_implements($class);

        $mocker = new ClassMocker();

        foreach ($implements as $interface) {
            $mocker->addInterface($interface);
        }

        return static::codeToMock($mocker->extends($class)->getCode());
    }

    public static function method(Closure $arg): MockMethod
    {
        $ref = new ReflectionFunction($arg);

        return new MockMethod($ref);
    }

    /**
     * @template T
     * @param T $instance
     * @return T&MockedClassInterface
     */
    public static function partial(mixed $instance): mixed
    {
        $mock = static::class($instance::class);
        $mock->__makePartial($instance);

        return $mock;
    }

    private static function codeToMock(string $code): MockedClassInterface
    {
        /** @var MockedClassInterface */
        $mock = eval($code);

        $mock->__setQuine($code);

        return $mock;
    }

    /**
     * @template T
     * @param Closure(Closure(Closure $expectedMethod): Expectation $expect): void $expectation
     */
    public static function expect(Closure $expectation): void
    {
        $mockExpector = new MockExpector();
        $mockExpector->validate($expectation);
    }
}
