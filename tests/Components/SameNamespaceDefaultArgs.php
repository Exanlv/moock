<?php

declare(strict_types=1);

namespace Tests\Components;

class SameNamespaceDefaultArgs
{
    public function methodEmpty(MixedConstructor $prop = new MixedConstructor()): bool
    {
        return false;
    }

    public function methodWithString(MixedConstructor $prop = new MixedConstructor('::same namespace string::')): bool
    {
        return false;
    }
}
