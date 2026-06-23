<?php

declare(strict_types=1);

namespace Tests\Components;

class FqcnDefaultArgs
{
    public function methodEmpty(\Tests\Components\MixedConstructor $prop = new \Tests\Components\MixedConstructor()): bool
    {
        return false;
    }

    public function methodWithString(\Tests\Components\MixedConstructor $prop = new \Tests\Components\MixedConstructor('::fqcn string::')): bool
    {
        return false;
    }
}
