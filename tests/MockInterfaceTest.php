<?php

declare(strict_types=1);

namespace Tests;

use Exan\Moock\Mock;
use PHPUnit\Framework\TestCase;
use Tests\Components\AnotherTestInterface;
use Tests\Components\TestInterface;

class MockInterfaceTest extends TestCase
{
    public function test_it_creates_a_mock_for_interface(): void
    {
        $mock = Mock::interface(TestInterface::class);

        static::assertInstanceOf(TestInterface::class, $mock);
    }

    public function test_it_can_replace_methods(): void
    {
        $mock = Mock::interface(TestInterface::class);

        Mock::method($mock->myMethod(...))
            ->replace(fn () => '::return value::');

        static::assertEquals('::return value::', $mock->myMethod());
    }

    public function test_it_keeps_track_of_amount_of_calls(): void
    {
        $mock = Mock::interface(TestInterface::class);

        Mock::method($mock->myMethod(...))
            ->replace(fn () => '::return value::');

        $mock->myMethod();
        $mock->myMethod();
        $mock->myMethod();
        $mock->myMethod();

        Mock::method($mock->myMethod(...))
            ->should()->haveBeenCalledTimes(4);
    }

    public function test_method_input_is_passed_to_replacement(): void
    {
        $mock = Mock::interface(TestInterface::class);

        Mock::method($mock->myOtherMethod(...))
            ->replace(function (string $inputA, string $inputB) {
                $this->assertEquals('::input a::', $inputA);
                $this->assertEquals('::input b::', $inputB);
            });

        $mock->myOtherMethod('::input a::', '::input b::');

        Mock::method($mock->myOtherMethod(...))
            ->should()->haveBeenCalledOnce();
    }

    public function test_it_can_mock_several_interfaces()
    {
        $mock = Mock::interfaces(TestInterface::class, AnotherTestInterface::class);

        static::assertInstanceOf(TestInterface::class, $mock);
        static::assertInstanceOf(AnotherTestInterface::class, $mock);
    }

    public function test_it_can_partially_mock(): void
    {
        $mock = Mock::interface(TestInterface::class);

        /**
         * Note: object being spied on does NOT have to implement any interfaces and such, even
         * if the actual mock object does implement said interfaces.
         */
        $spyOn = new class () {
            public bool $wasCalled = false;

            public function myMethod()
            {
                $this->wasCalled = true;

                return '::return value::';
            }
        };

        Mock::partial($mock, $spyOn);

        static::assertEquals('::return value::', $mock->myMethod());
        static::assertTrue($spyOn->wasCalled);
    }

    public function test_it_can_overwrite_methods_on_partial_mocks()
    {
        $mock = Mock::interface(TestInterface::class);

        /**
         * Note: object being spied on does NOT have to implement any interfaces and such, even
         * if the actual mock object does implement said interfaces.
         */
        $spyOn = new class () {
            public bool $wasCalled = false;

            public function myMethod()
            {
                $this->wasCalled = true;

                return '::return value::';
            }
        };

        Mock::partial($mock, $spyOn);

        Mock::method($mock->myMethod(...))->replace(fn () => '::other return value::');

        static::assertEquals('::other return value::', $mock->myMethod());
        static::assertFalse($spyOn->wasCalled);
    }
}
