<?php

declare(strict_types=1);

namespace Tests\Components;

trait SelfParamStuff
{
    public self $self;

    public function someMethod(self $arg): self
    {
        return $arg;
    }
}
