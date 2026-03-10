<?php

namespace Tests;

use Exan\Moock\Mock;
use PHPUnit\Framework\TestCase;
use Tests\Components\PropertiesTestClass;

class MockPropertiesTest extends TestCase
{
    public function test_it_forwards_a_property()
    {
        $mock = Mock::class(PropertiesTestClass::class);
        $partial = new class {
            public string $myString = 'my other string';
            public int $myInt = 321;
        };

        Mock::partial($mock, $partial);

        Mock::properties($mock)
            ->forward($mock->myString, $mock->myInt);

        $this->assertEquals('my other string', $mock->myString);
        $this->assertEquals(321, $mock->myInt);

        $partial->myString = 'yet another string';
        $partial->myInt = 456;

        $this->assertEquals('yet another string', $mock->myString);
        $this->assertEquals(456, $mock->myInt);
    }

    public function test_it_can_forward_properties_on_anonymous_classes()
    {
        $this->markTestSkipped();

        $class = new class {
            public string $myString = 'my string';
            public int $myInt = 123;
        };

        $mock = Mock::class($class::class);

        $partial = new class {
            public string $myString = 'my other string';
            public int $myInt = 321;
        };

        Mock::partial($mock, $partial);

        Mock::properties($mock)
            ->forward($mock->myString, $mock->myInt);

        $this->assertEquals('my other string', $mock->myString);
        $this->assertEquals(321, $mock->myInt);

        $partial->myString = 'yet another string';
        $partial->myInt = 456;

        $this->assertEquals('yet another string', $mock->myString);
        $this->assertEquals(456, $mock->myInt);
    }
}
