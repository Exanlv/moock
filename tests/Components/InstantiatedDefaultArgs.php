<?php

declare(strict_types=1);

namespace Tests\Components;

class InstantiatedDefaultArgs
{
    public function methodDefaultEmpty(MixedConstructor $prop = new MixedConstructor())
    {
    }

    public function methodDefaultString(MixedConstructor $prop = new MixedConstructor('::my string::'))
    {
    }
}
