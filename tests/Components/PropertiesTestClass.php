<?php

namespace Tests\Components;

class PropertiesTestClass
{
    public string $myFirstProp = '::first string::';
    public string $mySecondProp = '::second string::';
    public string $myThirdProp = '::third string::';
    public string $myFourthProp = '::fourth string::';

    final public string $finalString;
    public protected(set) string $protectedSetString;
    public private(set) string $privateSetString;
}
