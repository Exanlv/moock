<?php

declare(strict_types=1);

namespace Exan\Moock;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use RuntimeException;

class Mock
{
    /**
     * @template T
     * @param class-string<T> $class
     * @return T&MockedClassInterface
     */
    public static function interface($interface): mixed
    {
        return null;
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return T&MockedClassInterface
     */
    public static function class($class): mixed
    {
        if (!class_exists($class)) {
            throw new RuntimeException('Invalid class');
        }

        $ref = new ReflectionClass($class);

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

        $replacement = implode(PHP_EOL, $methodReplacements);

        $mockedClassInterface = MockedClassInterface::class;
        $mockedClassTrait = MockedClass::class;

        $creator = <<<PHP
            return new class extends $class implements \\$mockedClassInterface {
                use \\$mockedClassTrait;

                public function __construct() { }

                $replacement
            };
        PHP;

        $instance = eval($creator);

        return $instance;
    }

    /**
     * @param ReflectionMethod[] $methods
     *
     * @return string[]
     */
    private static function getSignatures(array $methods): array
    {
        return array_map(function (ReflectionMethod $method) {
            $parameters = $method->getParameters();

            return implode(', ', array_map(self::getParameterSignature(...), $parameters));
        }, $methods);
    }

    private static function getParameterSignature(ReflectionParameter $parameter): string
    {
        $type = $parameter->getType();
        return self::getTypeSignature($type) . ' $' . $parameter->getName();
    }

    private static function getTypeSignature(ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type): string
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

    public static function method(Closure $arg)
    {
        $ref = new ReflectionFunction($arg);

        return new MockMethod($ref);
    }
}
