<?php

namespace Tests\Components;

class PropertiesGetterTestClass
{
    public function __get($name)
    {
        if ($name === 'one') {
            return '::one::';
        }

        if ($name === 'other') {
            return '::other::';
        }
    }
}
