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

        $this->assertInstanceOf(TestClass::class, $mock);
    }

    public function test_it_can_replace_methods(): void
    {
        $mock = Mock::class(TestClass::class);

        Mock::method($mock->myMethod(...))
            ->replace(fn () => '::return value::');

        $this->assertEquals('::return value::', $mock->myMethod());
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

        $this->assertEquals(4, Mock::method($mock->myMethod(...))->calls());
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

        $this->assertEquals(
            ['::input b::', '::input a::'],
            $mock->myOtherMethod('::input a::', '::input b::')
        );

        $this->assertEquals(1, Mock::method($mock->myOtherMethod(...))->calls());
    }

    public function test_it_can_partially_mock(): void
    {
        $mock = Mock::class(TestClass::class);

        /**
         * Note: object being spied on does NOT have to implement any interfaces and such, even
         * if the actual mock object does implement said interfaces.
         */
        $spyOn = new class {
            public bool $wasCalled = false;

            public function myMethod()
            {
                $this->wasCalled = true;

                return '::return value::';
            }
        };

        Mock::partial($mock, $spyOn);

        $this->assertEquals('::return value::', $mock->myMethod());

        $this->assertTrue($spyOn->wasCalled);
    }

    public function test_it_can_overwrite_methods_on_partial_mocks()
    {
        $mock = Mock::class(TestClass::class);

        /**
         * Note: object being spied on does NOT have to implement any interfaces and such, even
         * if the actual mock object does implement said interfaces.
         */
        $spyOn = new class {
            public bool $wasCalled = false;

            public function myMethod()
            {
                $this->wasCalled = true;

                return '::return value::';
            }
        };

        Mock::partial($mock, $spyOn);

        Mock::method($mock->myMethod(...))->replace(fn () => '::other return value::');

        $this->assertEquals('::other return value::', $mock->myMethod());
        $this->assertFalse($spyOn->wasCalled);
    }
}