<?php

namespace Tests\Components;

class PropertiesTestClass
{
    final public string $finalString;
    public protected(set) string $protectedSetString;
    public private(set) string $privateSetString;
}
