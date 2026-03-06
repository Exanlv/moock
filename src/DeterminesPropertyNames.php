<?php

declare(strict_types=1);

namespace Exan\Moock;

use ReflectionClass;
use RuntimeException;
use Throwable;

/**
 * @property-read string[] $publicProperties
 * @property-read object $object
 */
trait DeterminesPropertyNames
{
    private array $abs = [
        'boolean' => [false, true],
        'integer' => [69, 420],
        'double' => [123.456, 456.123],
        'string' => ['Why did the chicken cross the road?', 'To get to the other side'],
        'array' => [['This is an item'], ['This is too']],
    ];

    /** @return string[] */
    protected function determinePropNames(&$ref): array
    {
        $type = gettype($ref);

        if (isset($this->abs[$type])) {
            [$a, $b] = $this->abs[$type];

            if (!$this->isReadonly($ref, $a)) {
                return [$this->determineVarNameByAB($a, $b, $ref)];
            }
        }

        if (is_object($ref)) {
            return $this->determinePropNameByFakeObject($ref);
        }

        if (is_resource($ref)) {
            return $this->determineVarNameByResourceId(get_resource_id($ref));
        }

        return $this->findByEquality($ref);
    }

    private function isReadonly(&$ref, $a)
    {
        $original = $ref;

        try {
            $ref = $a;
            return false;
        } catch (Throwable) {
            return true;
        } finally {
            $ref = $original;
        }
    }

    protected function determinePropNameByFakeObject(&$ref): array
    {
        $original = $ref;
        $replacement = (new ReflectionClass($ref::class))->newInstanceWithoutConstructor();

        $ref = $replacement;

        foreach ($this->publicProperties as $property) {
            if ($this->object->{$property} === $replacement) {
                $ref = $original;

                return [$property];
            }
        }

        return [];
    }

    protected function determineVarNameByResourceId(int $resourceId): array
    {
        return array_values(array_filter(
            $this->publicProperties,
            fn (string $property) => is_resource($this->object->{$property}) &&
                get_resource_id($this->object->{$property}) === $resourceId,
        ));
    }

    protected function findByEquality(mixed $value): array
    {
        return array_values(array_filter(
            $this->publicProperties,
            fn (string $property) => $this->object->{$property} === $value,
        ));
    }

    private function determineVarNameByAB(mixed $a, mixed $b, &$ref): string
    {
        $original = $ref;

        $ref = $a;

        $limits = array_filter(
            $this->publicProperties,
            fn (string $property) => $this->object->{$property} === $a
        );

        $ref = $b;

        $prop = array_filter(
            $limits,
            fn (string $property) => $this->object->{$property} === $b
        );

        $ref = $original;

        if (count($prop) === 0) {
            throw new RuntimeException('Unable to retrieve property name on object via a-b');
        }

        if (count($prop) === 1) {
            return reset($prop);
        }

        throw new RuntimeException('What in the world are you doing to your properties?');
    }
}
