<?php

declare(strict_types=1);

namespace Tests\Components;

use Tests\Components\MixedConstructor;

abstract class InstantiatedDefaultArgsAbstractClass
{
    abstract public function methodEmpty(MixedConstructor $prop = new MixedConstructor()): bool;

    abstract public function methodWithString(MixedConstructor $prop = new MixedConstructor('::interface string::')): bool;
}
