<?php

namespace Tests\Components;

class PropertiesGetterTestClass
{
    public function __construct(public readonly array $vars)
    {
    }

    public function __get($name)
    {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        }
    }
}
