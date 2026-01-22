<?php

declare(strict_types=1);

namespace Tests\Components;

class TestClass
{
    public function myMethod(): string
    {
        return '::original value::';
    }

    public function myOtherMethod(string $inputA, string $inputB): array
    {
        return [$inputA, $inputB];
    }

    public function testWithStringDefault(string $input = 'my-string'): void
    {
    }

    public function testWithArrayDefault(array $input = ['key' => 'value']): void
    {
    }

    public function testWithNullDefault(?array $input = null): void
    {
    }

    public function testWithTrueDefault(bool $input = true): void
    {
    }

    public function testWithFalseDefault(bool $input = false): void
    {
    }
}
