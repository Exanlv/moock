<?php

declare(strict_types=1);

namespace Exan\Moock\Methods;

use Exan\Moock\Analyzer\Utilize;
use Exan\Moock\Analyzer\Yanker;
use Exan\Moock\Formatting\Variables as FormatsVariables;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

/**
 * @internal
 */
class Mocker
{
    use FormatsVariables;

    public readonly array $methods;

    /** @param class-string[] $interfaces */
    public function __construct(
        public readonly array $interfaces,
    ) {
        /** @var list<list<ReflectionMethod>> */
        $allMethodsPerInterface = array_map(
            /**
             * @return list<ReflectionMethod>
             */
            function (string $interface): array {
                $ref = new ReflectionClass($interface);

                return $ref->getMethods(ReflectionMethod::IS_PUBLIC);
            },
            $this->interfaces
        );

        $allMethods = array_merge(...$allMethodsPerInterface);

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
                ? $this->extractDefaultParamSignature($parameter, $declaringMethod)
                : $this->formatValue($defaultValue));
        }

        return $signature;
    }

    private function extractDefaultParamSignature(ReflectionParameter $parameter, ReflectionMethod $method): string
    {
        // public function myMethod(MyClass $myArg = new MyClass())
        // Unfortunately, reflection only gives the exact instance of the default value. It is therefore impossible to recreate the
        // code used to instantiate an object based on reflection. It needs to be extracted from the original file instead.

        $class = $method->getDeclaringClass();

        $fileName = $class->getFileName();

        if (str_contains($fileName, 'eval()\'d code')) {
            throw new RuntimeException(
                sprintf(
                    'Unable to retrieve class construction in default parameters from eval-based class `%s`',
                    $fileName
                )
            );
        }

        $fileContents = file_get_contents($class->getFileName());
        if ($fileContents === false) {
            throw new RuntimeException(
                sprintf('Unable to load source file for class %s', $class->getFileName())
            );
        }

        if ($class->isAnonymous()) {
            $fullName = explode(':', $class->getName());
            $className = array_pop($fullName);
        } else {
            $fullName = explode('\\', $class->getName());
            $className = array_pop($fullName);
        }

        $yanked = Yanker::fetch($fileContents, [$className, $method->getName(), '$' . $parameter->getName()]);

        $utilize = Utilize::fromTokens(
            $yanked->namespace === null ? null : $yanked->namespace[2][1],
            $yanked->uses
        );

        $arg = $yanked->arg;
        if ($arg === null) {
            throw new RuntimeException(
                sprintf('Unable to retrieve constructor args for %s::%s()', $class->getName(), $method->getName())
            );
        }

        array_shift($arg); // new
        array_shift($arg); // (whitespace)
        array_shift($arg); // (classname)

        $flattenedTokens = array_map(function (string|array $token, int $index) use ($utilize, $arg) {
            if (is_string($token)) {
                return $token;
            }

            $argIsColon = fn (int $i): bool => isset($arg[$i]) && $arg[$i] === ':';
            $argIsWhitespace = fn (int $i): bool => isset($arg[$i]) && is_array($arg[$i]) && $arg[$i][0] === T_WHITESPACE;

            if (
                $token[0] === T_STRING
                && !($argIsColon($index + 1))
                && !($argIsWhitespace($index + 1) && $argIsColon($index + 2))
            ) {
                return $utilize->fullyQuantify($token[1]);
            }

            return $token[1];
        }, $arg, array_keys($arg));

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
