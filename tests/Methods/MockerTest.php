<?php

declare(strict_types=1);

namespace Tests\Methods;

use Closure;
use Exan\Moock\Methods\Mocker;
use Exan\Moock\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\InstantiatedDefaultArgs;
use Tests\Components\MixedConstructor;

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

    public function assertStringContainsInOrder(string $haystack, array $needles): void
    {
        foreach ($needles as $needle) {
            static::assertStringContainsString($needle, $haystack);
            $pos = strpos($haystack, $needle);
            $pos += strlen($needle);

            $haystack = substr($haystack, $pos);
        }
    }

    #[Test]
    #[DataProvider('objectInstantiationDataProvider')]
    public function it_instantiates_objects_for_arg_defaults(string $method, Closure $validator): void
    {
        $mock = Mock::class(InstantiatedDefaultArgs::class);

        Mock::method($mock->{$method}(...))->replace($validator);

        $this->assertTrue($mock->{$method}());
    }

    public static function objectInstantiationDataProvider(): array
    {
        return [
            'Empty constructor' => [
                'methodDefaultEmpty',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === null,
            ],
            'String in constructor' => [
                'methodDefaultString',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === '::my string::',
            ],
            'Recursive empty constructor' => [
                'methodDefaultRecursiveEmpty',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property instanceof MixedConstructor
                        && $mixedConstructor->property->property === null,
            ],
            'Recursive constructor with string' => [
                'methodDefaultRecursiveWithString',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property instanceof MixedConstructor
                        && $mixedConstructor->property->property === '::nested string::',
            ],
            'PHP variable syntax in string' => [
                'methodDefaultPhpVariableSyntaxString',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === '$variable',
            ],
            'PHP tag syntax in string' => [
                'methodDefaultPhpTagSyntaxString',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === '<?php echo "test"; ?>',
            ],
            'New keyword in string' => [
                'methodDefaultNewKeywordInString',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === 'new ClassName()',
            ],
        ];
    }
}
