<?php

declare(strict_types=1);

namespace Tests\Components;

use Tests\Components\MixedConstructor;

interface InstantiatedDefaultArgsInterface
{
    public function methodEmpty(MixedConstructor $prop = new MixedConstructor()): bool;

    public function methodWithString(MixedConstructor $prop = new MixedConstructor('::interface string::')): bool;
}
