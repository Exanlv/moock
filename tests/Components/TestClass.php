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
}
