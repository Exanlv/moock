<?php

declare(strict_types=1);

namespace Exan\Moock\Expector;

use Closure;

class Expectation
{
    public private(set) array $with;

    public function __construct(private readonly Closure $closure)
    {
    }

    public function with(array $with): void
    {
        $this->with = $with;
    }
}
