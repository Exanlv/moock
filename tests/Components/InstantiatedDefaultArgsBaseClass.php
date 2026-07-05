<?php

declare(strict_types=1);

namespace Tests\Components;

use Tests\Components\MixedConstructor;

class InstantiatedDefaultArgsBaseClass
{
    public function methodEmpty(MixedConstructor $prop = new MixedConstructor()): bool
    {
        return true;
    }

    public function methodWithString(MixedConstructor $prop = new MixedConstructor('::interface string::')): bool
    {
        return true;
    }
}
