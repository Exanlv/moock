<?php

declare(strict_types=1);

namespace Exan\Moock\Properties;

use Exan\Moock\Formatting\Variables as FormatsVariables;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * @internal
 */
class Mocker
{
    use FormatsVariables;

    /** @var null|class-string */
    public readonly ?string $parent;

    /**
     * @param class-string $toMock
     */
    public function __construct(
        public readonly string $toMock,
    ) {
        assert(str_contains($toMock, '@anonymous'), 'toMock must be an anonymous class');

        $parent = get_parent_class($toMock);
        $this->parent = $parent === false ? null : $parent;
    }

    public function getCode(): string
    {
        $properties = $this->getPropertiesToMock();
        $replicated = '';

        foreach ($properties as $property) {
            $replicated .= $this->getFormattedProperty($property) . PHP_EOL;
        }

        return $replicated;
    }

    private function getFormattedProperty(ReflectionProperty $property): string
    {
        $signature = '';

        if ($property->isFinal()) {
            $signature .= 'final ';
        }

        $signature .= 'public ';

        if ($property->isReadOnly()) {
            $signature .= 'readonly ';
        } elseif ($property->isPrivateSet()) {
            $signature .= 'private(set) ';
        } elseif ($property->isProtectedSet()) {
            $signature .= 'protected(set) ';
        }

        if ($property->isStatic()) {
            $signature .= 'static ';
        }

        if ($property->hasType()) {
            $signature .= $this->getTypeSignature($property->getType()) . ' ';
        }

        $signature .= '$' . $property->getName();

        if ($property->hasDefaultValue()) {
            $signature .= ' = ' . $this->formatValue($property->getDefaultValue());
        }

        if (!$property->isReadOnly() && !$property->isStatic()) {
            /**
             * "Abuse" hooks to ""properly"" mock properties
             */
            $signature .= '{' . PHP_EOL;
            $signature .= 'get {';
            $signature .= '$overwrite = $this->__moockPropertyGet(\'' . $property->getName() . '\');';
            $signature .= 'if ($overwrite->hasValue) return $overwrite->value;';
            $signature .= 'return $this->' . $property->getName() . '; }' . PHP_EOL;
            $signature .= 'set { $this->' . $property->getName() . ' = $this->__mockPropertySet(\'' . $property->getName() . '\', $value); }' . PHP_EOL;
            $signature .= '}' . PHP_EOL;
        } else {
            /**
             * Property hooks can not have a trailing ';', thus omit here
             */
            $signature .= ';';
        }

        return $signature;
    }

    /**
     * @return ReflectionProperty[]
     *
     * @psalm-return array<int<0, max>, ReflectionProperty>
     */
    private function getPropertiesToMock(): array
    {
        $mockingRef = new ReflectionClass($this->toMock);

        $ofMockingClass = $mockingRef->getProperties(ReflectionProperty::IS_PUBLIC);

        if ($this->parent === null) {
            return $ofMockingClass;
        }

        $parentRef = new ReflectionClass($this->parent);

        return array_filter(
            $ofMockingClass,
            function (ReflectionProperty $property) use ($parentRef) {
                try {
                    $parentMethod = $parentRef->getProperty($property->name);

                    return !$parentMethod->isFinal();
                } catch (ReflectionException) {
                    return true;
                }
            },
        );
    }
}
