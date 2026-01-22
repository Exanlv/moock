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
    private array $skipMethods = ['__construct'];

    /**
     * @param string[] $implements
     */
    public function __construct(
        private readonly array $inherits = [],
        private readonly ?string $extends = null,
        private readonly array $implements = [],
    ) {}

    public function getCode(): mixed
    {
        $replacements = [];

        foreach ($this->inherits as $interface) {
            if (!interface_exists($interface) && !class_exists($interface)) {
                throw new RuntimeException('Invalid class or interface ' . $interface);
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
            $this->implements,
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
            fn (ReflectionMethod $method) => !in_array($method->name, $this->skipMethods),
        );

        $this->skipMethods = [
            ...$this->skipMethods,
            ...array_map(fn (ReflectionMethod $method) => $method->name, $methodsToReplace),
        ];

        $signatures = self::getSignatures($methodsToReplace);

        $methodReplacements = array_map(
            function (ReflectionMethod $method, string $signature) {
                $name = $method->name;

                $returnSignature = $method->hasReturnType()
                    ? ': ' . self::getTypeSignature($method->getReturnType())
                    : '';

                $isVoid = $method->hasReturnType() &&
                    $method->getReturnType() instanceof ReflectionNamedType &&
                    $method->getReturnType()->isBuiltin() &&
                    $method->getReturnType()->getName() === 'void';

                $return = $isVoid ? '' : 'return';

                return <<<FUNC
                    public function $name($signature) $returnSignature {
                        $return \$this->__moockFunctionCall('$name', func_get_args());
                    }
                FUNC;
            },
            $methodsToReplace,
            $signatures,
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
