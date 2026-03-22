<?php

declare(strict_types=1);

namespace Exan\Moock\Formatting;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;

/**
 * @internal
 */
trait Variables
{
    private function formatValue(mixed $value): string
    {
        if (is_string($value)) {
            $value = '\'' . str_replace('\'', '\\\'', $value) . '\'';
        }

        if (is_array($value)) {
            $formattedArray = '[';

            foreach ($value as $key => $x) {
                $formattedArray .= self::formatValue($key) . ' => ' . self::formatValue($x) . ', ';
            }

            return $formattedArray . ']';
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        return is_null($value)
            ? 'null'
            : (string) $value;
    }

    private function getTypeSignature(ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type): string
    {
        if ($type === null) {
            return '';
        }

        $types = [];

        if ($type instanceof ReflectionNamedType) {
            $types[] = $this->isSemiBuiltIn($type) ? $type->getName() : '\\' . $type->getName();
        } else {
            $types = array_map(
                fn (ReflectionNamedType $subType) => $this->isSemiBuiltIn($subType) ? $subType->getName() : '\\' . $subType->getName(),
                $type->getTypes(),
            );
        }

        if ($type->allowsNull()) {
            $types[] = 'null';
        }

        $seperator = $type instanceof ReflectionIntersectionType
            ? '&'
            : '|';

        $types = array_unique($types);

        return in_array('mixed', $types)
            ? 'mixed'
            : implode($seperator, $types);
    }

    private function isSemiBuiltIn(ReflectionNamedType $type): bool
    {
        return $type->isBuiltin()
            || in_array($type->getName(), ['self', 'static']);
    }
}
