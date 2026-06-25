<?php

declare(strict_types=1);

namespace Tests\Components;

class MixedConstructor
{
    public function __construct(public mixed $property = null) {}
}
