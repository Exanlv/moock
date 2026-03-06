<?php

declare(strict_types=1);

namespace Exan\Moock;

use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

class PropertyMocker
{
    /** @var string[] */
    private readonly array $publicProperties;

    public function __construct(
        private readonly object $object,
    ) {
        $this->publicProperties = array_map(
            fn (ReflectionProperty $prop) => $prop->name,
            (new ReflectionClass($object))->getProperties(ReflectionProperty::IS_PUBLIC)
        );
    }

}
