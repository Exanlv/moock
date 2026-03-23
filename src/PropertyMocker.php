<?php

declare(strict_types=1);

namespace Exan\Moock;

use ReflectionClass;
use ReflectionProperty;

class PropertyMocker
{
    use DeterminesPropertyNames;

    /** @var string[] */
    private readonly array $publicProperties;

    public function __construct(
        private readonly MockedClassInterface $object,
    ) {
        $this->publicProperties = array_map(
            fn (ReflectionProperty $prop) => $prop->name,
            (new ReflectionClass($object))->getProperties(ReflectionProperty::IS_PUBLIC),
        );
    }

    public function forward(mixed &...$properties)
    {
        foreach ($properties as &$property) {
            $this->forwardProperties($this->determinePropNames($property));
        }
    }

    private function forwardProperties(array $properties)
    {
        foreach ($properties as $property) {
            $this->object->__forwardProp($property);
        }
    }
}
