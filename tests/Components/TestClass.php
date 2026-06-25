<?php

declare(strict_types=1);

namespace Tests\Components;

class TestClass
{
    public $myMethodWasCalled = false;

    public function myMethod(): string
    {
        $this->myMethodWasCalled = true;

        return '::original value::';
    }

    public function myVoidMethod(): void
    {
        $this->myMethodWasCalled = true;
    }

    public function myOtherMethod(string $inputA, string $inputB): array
    {
        return [$inputA, $inputB];
    }

    public function returnVoid(): void {}

    public function testWithStringDefault(string $input = 'my-string'): void {}

    public function testWithArrayDefault(array $input = ['key' => 'value']): void {}

    public function testWithNullDefault(?array $input = null): void {}

    public function testWithTrueDefault(bool $input = true): void {}

    public function testWithFalseDefault(bool $input = false): void {}

    public function testWithDualReturnType(bool $input = false): AnotherTestInterface|TestInterface
    {
        return new class () implements TestInterface {
            public function myMethod(): void
            {
                throw new \Exception('Not implemented');
            }

            public function myOtherMethod(string $inputA, string $inputB): void
            {
                throw new \Exception('Not implemented');
            }
        };
    }

    public function testWithIntersectionReturnType(): AnotherTestInterface&TestInterface
    {
        return new class () implements AnotherTestInterface, TestInterface {
            public function myMethod(): void
            {
                throw new \Exception('Not implemented');
            }

            public function anotherMethod(): void
            {
                throw new \Exception('Not implemented');
            }

            public function yetAnotherMethod(string $inputA, string $inputB): void
            {
                throw new \Exception('Not implemented');
            }

            public function myOtherMethod(string $inputA, string $inputB): void
            {
                throw new \Exception('Not implemented');
            }
        };
    }

    public function testMixedTypes(mixed $myvar): mixed
    {
        return '';
    }

    public function testReturnSelf(): self
    {
        return new self();
    }

    public function testReturnStatic(): static
    {
        return new static();
    }

    public function testReturnNullAsType(): int|string|null
    {
        return 1;
    }

    public static function someStaticMethod(): void {}

    final public function myFinalMethod(): void {}

    public function testSpreadArg(int ...$myInts): void {}

    public function testParameterAsReference(string &$myVar): void {}

    public function testSpreadParameterAsReference(string &...$myVar): void {}

    public function testReturnNever(): never
    {
        die();
    }

    public function testDefaultNewClassValue(UserService $userService = new UserService()): void {}
}
