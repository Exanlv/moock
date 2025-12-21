<?php

namespace Exan\Moock;

use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use RuntimeException;

class MockClassBuilder
{
    /**
     * @param string[] $implements
     */
    public function __construct(
        private readonly ?string $extends = null,
        private readonly array $implements = [],
    ) {
    }

    public function getCode(): mixed
    {
        $replacements = [];

        if ($this->extends !== null) {
            if (!class_exists($this->extends)) {
                throw new RuntimeException('Invalid class ' . $this->extends);
            }

            $ref = new ReflectionClass($this->extends);
            $replacements[] = $this->getMethodReplacements($ref);
        }

        foreach ($this->implements as $interface) {
            if (!interface_exists($interface)) {
                throw new RuntimeException('Invalid interface ' . $interface);
            }

            $ref = new ReflectionClass($interface);
            $replacements[] = $this->getMethodReplacements($ref);
        }

        $creator = 'return new class ';
        if ($this->extends !== null) {
            $creator .= 'extends \\' . $this->extends . ' ';
        }

        $quantifiedImplements = array_map(
            fn ($interface) => '\\' . $interface,
            $this->implements
        );

        $quantifiedImplements[] = '\\' . MockedClassInterface::class;

        $creator .= 'implements ' . implode(', ', $quantifiedImplements);

        $creator .= '{' . PHP_EOL;
        $creator .= 'use \\' . MockedClass::class . ';' . PHP_EOL;
        $creator .= 'public function __construct() { }' . PHP_EOL;

        $creator .= implode(PHP_EOL, $replacements);

        $creator .= PHP_EOL . '};';

        return $creator;
    }

    private function getMethodReplacements(ReflectionClass $ref): string
    {
        $methodsToReplace = array_filter(
            $ref->getMethods(ReflectionMethod::IS_PUBLIC),
            fn (ReflectionMethod $method) => !in_array($method->name, ['__call', '__construct']),
        );

        $signatures = self::getSignatures($methodsToReplace);

        $methodReplacements = array_map(
            function (ReflectionMethod $method, string $signature) {
                $name = $method->name;

                $return = $method->hasReturnType()
                    ? ': ' . self::getTypeSignature($method->getReturnType())
                    : '';

                return <<<FUNC
                    public function $name($signature) $return   {
                        return \$this->__call('$name', func_get_args());
                    }
                FUNC;
            },
            $methodsToReplace, $signatures
        );

        return implode(PHP_EOL, $methodReplacements);
    }

    /**
     * @param ReflectionMethod[] $methods
     *
     * @return string[]
     */
    private function getSignatures(array $methods): array
    {
        return array_map(function (ReflectionMethod $method) {
            $parameters = $method->getParameters();

            return implode(', ', array_map(self::getParameterSignature(...), $parameters));
        }, $methods);
    }

    private function getParameterSignature(ReflectionParameter $parameter): string
    {
        $type = $parameter->getType();

        $signature = self::getTypeSignature($type) . ' $' . $parameter->getName();

        if ($parameter->isDefaultValueAvailable()) {
            $defaultValue = $parameter->getDefaultValue();

            $signature .= ' = ' . self::formatValue($defaultValue);
        }

        return $signature;
    }

    private function formatValue(mixed $value): string
    {
        if (is_string($value)) {
            $value = '\'' . str_replace('\'', '\\\'', $value) . '\'';
        }

        if (is_array($value)) {
            $formattedArray = '[';

            foreach ($value as $key => $x) {
                $formattedArray .= self::formatValue($key) . ' => ' . self::formatValue($x) . ',';
            }

            return $formattedArray . ']';
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
            $types[] = $type->isBuiltin() ? $type->getName() : '\\' . $type->getName();
        } else {
            $types = array_map(
                fn (ReflectionNamedType $subType) => $subType->isBuiltin() ? $subType->getName() : '\\' . $subType->getName(),
                $type->getTypes()
            );
        }

        if ($type->allowsNull()) {
            $types[] = 'null';
        }

        return implode('|', $types);
    }
}
