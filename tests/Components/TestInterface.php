<?php

declare(strict_types=1);

namespace Tests\Components;

interface TestInterface
{
    public function myMethod();
    public function myOtherMethod(string $inputA, string $inputB);
}
