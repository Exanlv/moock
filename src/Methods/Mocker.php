<?php

declare(strict_types=1);

namespace Exan\Moock\Methods;

use Exan\Moock\Extractor;
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

            if (in_array($methodName, ['__construct', '__get'])
                || in_array($methodName, $methodNames)
                || str_starts_with($methodName, '__moock')
                || $method->isStatic()
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
        $declaringClass = $method->getDeclaringClass();

        $functionArgs = implode(
            ', ',
            array_map(
                fn (ReflectionParameter $parameter) => $this->getParameterSignature($parameter, $method, $declaringClass),
                $method->getParameters(),
            ),
        );

        $moockFunctionCallArgs = $this->getInternalMockCallArgs($method);

        $returnSignature = $method->hasReturnType()
            ? ': ' . self::getTypeSignature($method->getReturnType(), $declaringClass)
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

    private function getParameterSignature(ReflectionParameter $parameter, ReflectionMethod $declaringMethod, ReflectionClass $declaringClass): string
    {
        $type = $parameter->getType();

        $variableIndicator = '$';

        if ($parameter->isVariadic()) {
            $variableIndicator = '...' . $variableIndicator;
        }

        if ($parameter->isPassedByReference()) {
            $variableIndicator = '&' . $variableIndicator;
        }

        $signature = $this->getTypeSignature($type, $declaringClass)
            . ' ' . $variableIndicator . $parameter->getName();

        if ($parameter->isDefaultValueAvailable()) {
            $defaultValue = $parameter->getDefaultValue();

            $signature .= ' = ' . (is_object($defaultValue)
                ? $this->extractDefaultParamSignature($parameter, $declaringMethod, $declaringClass)
                : $this->formatValue($defaultValue));
        }

        return $signature;
    }

    private function extractDefaultParamSignature(ReflectionParameter $parameter, ReflectionMethod $method, ReflectionClass $class): string
    {
        // public function myMethod(MyClass $myArg = new MyClass())
        // Unfortunately, reflection only gives the exact instance of the default value. It is therefore impossible to recreate the
        // code used to instantiate an object based on reflection. It needs to be extracted from the original file instead.

        $fileContents = file_get_contents($class->getFileName());

        $tokens = Extractor::lines(token_get_all($fileContents), $method->getStartLine(), $method->getEndLine());
        $method = Extractor::function($tokens, $method->getName());
        $arg = Extractor::arg($method, $parameter->getName());

        while(
            count($arg)
            && (!is_array($arg) || $arg[0][0] !== T_NEW)
        ) {
            array_shift($arg);
        }

        array_shift($arg); // new
        array_shift($arg); // (whitespace)
        array_shift($arg); // (classname)

        $flattenedTokens = array_map(fn (string|array $token) => is_array($token) ? $token[1] : $token, $arg);

        return 'new \\' . $parameter->getDefaultValue()::class . implode(' ', $flattenedTokens);
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
