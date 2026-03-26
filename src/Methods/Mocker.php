<?php

declare(strict_types=1);

namespace Exan\Moock\Methods;

use Exan\Moock\Formatting\Variables as FormatsVariables;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @internal
 */
class Mocker
{
    use FormatsVariables;

    public readonly array $methods;

    /** @param string[] */
    public function __construct(
        public readonly array $interfaces,
    ) {
        /** @var ReflectionMethod[] */
        $allMethods = array_merge(
            ...array_map(function (string $interface): array {
                $ref = new ReflectionClass($interface);

                return $ref->getMethods(ReflectionMethod::IS_PUBLIC);
            }, $this->interfaces),
        );

        $methodNames = [];
        $methodsToMock = [];

        foreach ($allMethods as $method) {
            $methodName = $method->getName();

            if ($methodName === '__construct'
                || in_array($methodName, $methodNames)
                || str_starts_with($methodName, '__moock')
                || $method->isStatic() // TODO: Why did I exclude these from being mocked in the first place?
                || $method->isFinal()
            ) {
                continue;
            }

            $methodNames[] = $methodName;
            $methodsToMock[] = $method;
        }

        $this->methods = $methodsToMock;
    }

    public function getCode(): string
    {
        return implode(PHP_EOL, array_map(
            $this->getFormattedMethod(...),
            $this->methods,
        ));
    }

    private function getFormattedMethod(ReflectionMethod $method): string
    {
        $name = $method->name;

        $functionArgs = implode(
            ', ',
            array_map($this->getParameterSignature(...), $method->getParameters()),
        );

        $moockFunctionCallArgs = $this->getInternalMockCallArgs($method);

        $returnSignature = $method->hasReturnType()
            ? ': ' . self::getTypeSignature($method->getReturnType())
            : '';

        $canReturn = $method->hasReturnType()
            && $method->getReturnType() instanceof ReflectionNamedType
            && $method->getReturnType()->isBuiltin()
            && in_array($method->getReturnType()->getName(), ['void', 'never']);

        $return = $canReturn ? '' : 'return';

        return <<<FUNC
            public function $name($functionArgs) $returnSignature {
                $return \$this->__moockFunctionCall('$name', compact($moockFunctionCallArgs));
            }
        FUNC;
    }

    private function getParameterSignature(ReflectionParameter $parameter): string
    {
        $type = $parameter->getType();

        $variableIndicator = '$';

        if ($parameter->isVariadic()) {
            $variableIndicator = '...' . $variableIndicator;
        }

        if ($parameter->isPassedByReference()) {
            $variableIndicator = '&' . $variableIndicator;
        }

        $signature = self::getTypeSignature($type)
            . ' ' . $variableIndicator . $parameter->getName();

        if ($parameter->isDefaultValueAvailable()) {
            $defaultValue = $parameter->getDefaultValue();

            $signature .= ' = ' . self::formatValue($defaultValue);
        }

        return $signature;
    }

    private function getInternalMockCallArgs(ReflectionMethod $method): string
    {
        $parameters = $method->getParameters();

        return $this->formatValue(
            array_map(
                fn (ReflectionParameter $parameter) => $parameter->name,
                $parameters,
            ),
        );
    }
}
