<?php

declare(strict_types=1);

namespace Tests;

use Exan\Moock\Mock;
use PHPUnit\Framework\TestCase;
use Tests\Components\TestClass;

class MockClassTest extends TestCase
{
    public function test_it_creates_a_mock_for_class(): void
    {
        $mock = Mock::class(TestClass::class);

        static::assertInstanceOf(TestClass::class, $mock);
    }

    public function test_it_can_replace_methods(): void
    {
        $mock = Mock::class(TestClass::class);

        Mock::method($mock->myMethod(...))
            ->replace(fn () => '::return value::');

        static::assertEquals('::return value::', $mock->myMethod());
    }

    public function test_it_does_not_require_replacing_methods_for_expectations(): void
    {
        $mock = Mock::class(TestClass::class);

        $mock->testWithTrueDefault();

        Mock::method($mock->testWithTrueDefault(...))
            ->expect()
            ->toHaveBeenCalled();
    }

    public function test_it_keeps_track_of_amount_of_calls(): void
    {
        $mock = Mock::class(TestClass::class);

        Mock::method($mock->myMethod(...))
            ->replace(fn () => '::return value::');

        $mock->myMethod();
        $mock->myMethod();
        $mock->myMethod();
        $mock->myMethod();

        Mock::method($mock->myMethod(...))
            ->expect()->toHaveBeenCalledTimes(4);
    }

    public function test_method_input_is_passed_to_replacement(): void
    {
        $mock = Mock::class(TestClass::class);

        Mock::method($mock->myOtherMethod(...))
            ->replace(function (string $inputA, string $inputB) {
                $this->assertEquals('::input a::', $inputA);
                $this->assertEquals('::input b::', $inputB);

                return [$inputB, $inputA];
            });

        static::assertEquals(
            ['::input b::', '::input a::'],
            $mock->myOtherMethod('::input a::', '::input b::'),
        );

        Mock::method($mock->myOtherMethod(...))
            ->expect()->toHaveBeenCalledOnce();
    }

    public function test_it_can_partially_mock(): void
    {
        $mock = Mock::class(TestClass::class);

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
        $mock = Mock::class(TestClass::class);

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

    public function test_it_can_set_arg_expectations()
    {
        $mock = Mock::class(TestClass::class);

        Mock::method($mock->myOtherMethod(...))->replace(fn () => ['::return value::']);

        $mock->myOtherMethod('::1::', '::2::');
        $mock->myOtherMethod('::1::', '::2::');
        $mock->myOtherMethod('::1::', '::3::');
        $mock->myOtherMethod('::3::', '::3::');
        $mock->myOtherMethod('::3::', '::3::');

        Mock::method($mock->myOtherMethod(...))
            ->expect()
            ->with('::1::')
            ->toHaveBeenCalled();

        Mock::method($mock->myOtherMethod(...))
            ->expect()
            ->with('::1::')
            ->toHaveBeenCalledTimes(3);

        Mock::method($mock->myOtherMethod(...))
            ->expect()
            ->with('::1::', '::2::')
            ->toHaveBeenCalledTimes(2);

        Mock::method($mock->myOtherMethod(...))
            ->expect()
            ->with(fn (string $inputA) => $inputA === '::3::')
            ->toHaveBeenCalledTimes(2);

        Mock::method($mock->myOtherMethod(...))
            ->expect()
            ->with(inputB: fn (string $inputB) => $inputB === '::3::')
            ->toHaveBeenCalledTimes(3);
    }
}
