<?php

declare(strict_types=1);

namespace Tests;

use Exan\Moock\Mock;
use PHPUnit\Framework\TestCase;
use Tests\Components\AnotherTestInterface;
use Tests\Components\TestClass;
use Tests\Components\TestInterface;

class MockAnonymousClassTest extends TestCase
{
    public function test_it_mocks_anonymous_classes()
    {
        $object = new class () {
            public function myMethod() {}
        };

        $mock = Mock::class($object::class);

        static::assertTrue(method_exists($mock, 'myMethod'));
    }

    public function test_it_extends_same_parent_class()
    {
        $object = new class () extends TestClass {
            public function otherMethod() {}
        };

        $mock = Mock::class($object::class);

        static::assertTrue(method_exists($mock, 'otherMethod'));
        static::assertInstanceOf(TestClass::class, $mock);
    }

    public function test_it_implements_the_same_interfaces()
    {
        $object = new class () implements TestInterface, AnotherTestInterface {
            public function myMethod() {}

            public function anotherMethod() {}

            public function myOtherMethod(string $inputA, string $inputB) {}

            public function yetAnotherMethod(string $inputA, string $inputB) {}

            public function otherMethod() {}
        };

        $mock = Mock::class($object::class);

        static::assertTrue(method_exists($mock, 'otherMethod'));
        static::assertInstanceOf(TestInterface::class, $mock);
        static::assertInstanceOf(AnotherTestInterface::class, $mock);
    }
}
