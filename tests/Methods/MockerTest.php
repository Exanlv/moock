<?php

declare(strict_types=1);

namespace Tests\Methods;

use Exan\Moock\Methods\Mocker;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

class MockerTest extends TestCase
{
    #[Test]
    public function it_writes_mock_functions(): void
    {
        $class = new class () {
            public function myMethod(): void {}
        };

        $mocker = new Mocker([$class::class]);

        $code = $mocker->getCode();

        static::assertStringNotContainsString('return $this->__moockFunctionCall', $code);

        $this->assertStringContainsInOrder($code, [
            'public function myMethod',
            ': void',
            '$this->__moockFunctionCall(\'myMethod\', compact([]));',
        ]);
    }

    #[Test]
    public function it_handles_never_return_type(): void
    {
        $class = new class () {
            public function myMethod(): never
            {
                die();
            }
        };

        $mocker = new Mocker([$class::class]);

        $code = $mocker->getCode();

        static::assertStringNotContainsString('return $this->__moockFunctionCall', $code);

        $this->assertStringContainsInOrder($code, [
            'public function myMethod',
            ': never',
            '$this->__moockFunctionCall(\'myMethod\', compact([]));',
        ]);
    }

    #[Test]
    public function it_recreates_signature_args(): void
    {
        $class = new class () {
            public function myMethod(string $myArg = 'string', array $myArray = ['default' => 'value']): void {}
        };

        $mocker = new Mocker([$class::class]);

        $this->assertStringContainsInOrder($mocker->getCode(), [
            'public function myMethod',
            'string $myArg = \'string\'',
            'array $myArray = [\'default\' => \'value\', ]',
            ': void',
            '$this->__moockFunctionCall(\'myMethod\', compact([0 => \'myArg\', 1 => \'myArray\', ]));',
        ]);
    }

    /**
     * @param string[] $methods
     *
     * @return ReflectionMethod[]
     */
    private function getReflectionMethods(array $methods, ReflectionClass $ref): array
    {
        return array_map(fn (string $method) => $ref->getMethod($method), $methods);
    }

    public function assertStringContainsInOrder(string $haystack, array $needles): void
    {
        foreach ($needles as $needle) {
            static::assertStringContainsString($needle, $haystack);
            $pos = strpos($haystack, $needle);
            $pos += strlen($needle);

            $haystack = substr($haystack, $pos);
        }
    }
}
