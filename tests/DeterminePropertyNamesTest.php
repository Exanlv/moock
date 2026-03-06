<?php

declare(strict_types=1);

namespace Tests;

use Exan\Moock\DeterminesPropertyNames;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

class DeterminePropertyNamesTest extends TestCase
{
    #[Test]
    #[TestWith(['test value'])]
    #[TestWith(['test value', 'other test value'])]
    #[TestWith([123])]
    #[TestWith([123, 456])]
    #[TestWith([123.456])]
    #[TestWith([123.456, 'string'])]
    #[TestWith([[1, 2], 'string'])]
    #[TestWith([true, false])]
    #[TestWith([true])]
    public function it_determines_property_name_for_basic_types($myVar, $myOtherVar = null)
    {
        $myOtherVar ??= $myVar;

        $class = new class ($myVar, $myOtherVar) {
            public function __construct(
                public mixed $myVar,
                public mixed $myOtherVar,
            ) {
            }
        };

        $this->assertEquals(
            ['myVar'],
            $this->determinePropName($class, $class->myVar)
        );

        $this->assertEquals($myVar, $class->myVar);
    }

    #[Test]
    public function it_determines_property_name_for_resources()
    {
        $class = new class {
            public mixed $myOtherVar;
            public mixed $myVar;

            public function __construct() {
                $this->myVar = fopen('php://memory', 'r+');
                $this->myOtherVar = fopen('php://memory', 'r+');
            }
        };

        $this->assertEquals(
            ['myVar'],
            $this->determinePropName($class, $class->myVar)
        );

        $otherClass = new class {
            public mixed $myOtherVar;
            public mixed $myVar;

            public function __construct() {
                $resource = fopen('php://memory', 'r+');
                $this->myVar = $resource;
                $this->myOtherVar = $resource;
            }
        };

        $this->assertEquals(
            ['myOtherVar', 'myVar'],
            $this->determinePropName($otherClass, $otherClass->myVar)
        );
    }

    #[Test]
    public function it_determines_property_name_for_objects()
    {
        $class = new class {
            public mixed $myOtherVar;
            public mixed $myVar;

            public function __construct() {
                $this->myVar = new class {};
                $this->myOtherVar = new class {};
            }
        };

        $this->assertEquals(
            ['myVar'],
            $this->determinePropName($class, $class->myVar)
        );

        $otherClass = new class {
            public mixed $myOtherVar;
            public mixed $myVar;

            public function __construct() {
                $class = new class {};
                $this->myVar = $class;
                $this->myOtherVar = $class;
            }
        };

        $this->assertEquals(
            ['myVar'],
            $this->determinePropName($otherClass, $otherClass->myVar)
        );
    }

    private function determinePropName(object $object, &$ref)
    {
        $properties = (new ReflectionClass($object))->getProperties(ReflectionProperty::IS_PUBLIC);

        $class = new class($object, array_map(fn(ReflectionProperty $prop) => $prop->name, $properties)) {
            use DeterminesPropertyNames;

            public function __construct(
                protected readonly object $object,
                protected readonly array $publicProperties,
            ) {}

            public function getPropertyName(&$ref): array
            {
                return $this->determinePropNames($ref);
            }
        };

        return $class->getPropertyName($ref);
    }
}
