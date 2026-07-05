<?php

declare(strict_types=1);

namespace Tests\Methods;

use Closure;
use Exan\Moock\Methods\Mocker;
use Exan\Moock\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\FqcnDefaultArgs;
use Tests\Components\InstantiatedDefaultArgs;
use Tests\Components\InstantiatedDefaultArgsAbstractClass;
use Tests\Components\InstantiatedDefaultArgsExtension;
use Tests\Components\InstantiatedDefaultArgsInterface;
use Tests\Components\MixedConstructor;
use Tests\Components\SameNamespaceDefaultArgs;
use Tests\Components\WeirdFormattingDefaultArgs;

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
            'Aliased import - empty constructor' => [
                'methodAliasedEmpty',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === null,
            ],
            'Aliased import - string in constructor' => [
                'methodAliasedWithString',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === '::aliased string::',
            ],
            'Aliased import - recursive empty constructor' => [
                'methodAliasedRecursive',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property instanceof MixedConstructor
                        && $mixedConstructor->property->property === null,
            ],
            'Aliased import - recursive with string' => [
                'methodAliasedRecursiveWithString',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property instanceof MixedConstructor
                        && $mixedConstructor->property->property === '::aliased nested string::',
            ],
            'Individual aliased import - empty constructor' => [
                'methodIndividualAliasEmpty',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === null,
            ],
            'Individual aliased import - string in constructor' => [
                'methodIndividualAliasWithString',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === '::individual alias string::',
            ],
            'Sub-namespace group use - empty constructor' => [
                'methodSubNamespaceGroupUseEmpty',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === null,
            ],
            'Sub-namespace group use - string in constructor' => [
                'methodSubNamespaceGroupUseWithString',
                fn (MixedConstructor $mixedConstructor) => $mixedConstructor->property === '::sub namespace string::',
            ],
        ];
    }

    #[Test]
    #[DataProvider('fqcnObjectInstantiationDataProvider')]
    public function it_instantiates_objects_when_using_fqcn(string $method, Closure $validator): void
    {
        $mock = Mock::class(FqcnDefaultArgs::class);

        Mock::method($mock->{$method}(...))->replace($validator);

        $this->assertTrue($mock->{$method}());
    }

    public static function fqcnObjectInstantiationDataProvider(): array
    {
        return [
            'Empty constructor' => [
                'methodEmpty',
                fn (MixedConstructor $m) => $m->property === null,
            ],
            'String in constructor' => [
                'methodWithString',
                fn (MixedConstructor $m) => $m->property === '::fqcn string::',
            ],
        ];
    }

    #[Test]
    #[DataProvider('sameNamespaceObjectInstantiationDataProvider')]
    public function it_instantiates_objects_when_in_same_namespace(string $method, Closure $validator): void
    {
        $mock = Mock::class(SameNamespaceDefaultArgs::class);

        Mock::method($mock->{$method}(...))->replace($validator);

        $this->assertTrue($mock->{$method}());
    }

    public static function sameNamespaceObjectInstantiationDataProvider(): array
    {
        return [
            'Empty constructor' => [
                'methodEmpty',
                fn (MixedConstructor $m) => $m->property === null,
            ],
            'String in constructor' => [
                'methodWithString',
                fn (MixedConstructor $m) => $m->property === '::same namespace string::',
            ],
        ];
    }

    #[Test]
    #[DataProvider('interfaceObjectInstantiationDataProvider')]
    public function it_instantiates_objects_when_declared_on_interface(string $method, Closure $validator): void
    {
        $mock = Mock::interface(InstantiatedDefaultArgsInterface::class);

        Mock::method($mock->{$method}(...))->replace($validator);

        $this->assertTrue($mock->{$method}());
    }

    #[Test]
    #[DataProvider('interfaceObjectInstantiationDataProvider')]
    public function it_instantiates_objects_when_declared_on_abstract_class(string $method, Closure $validator): void
    {
        $mock = Mock::interface(InstantiatedDefaultArgsAbstractClass::class);

        Mock::method($mock->{$method}(...))->replace($validator);

        $this->assertTrue($mock->{$method}());
    }

    #[Test]
    #[DataProvider('interfaceObjectInstantiationDataProvider')]
    public function it_instantiates_objects_when_declared_on_parent_class(string $method, Closure $validator): void
    {
        $mock = Mock::interface(InstantiatedDefaultArgsExtension::class);

        Mock::method($mock->{$method}(...))->replace($validator);

        $this->assertTrue($mock->{$method}());
    }

    public static function interfaceObjectInstantiationDataProvider(): array
    {
        return [
            'Empty constructor' => [
                'methodEmpty',
                fn (MixedConstructor $m) => $m->property === null,
            ],
            'String in constructor' => [
                'methodWithString',
                fn (MixedConstructor $m) => $m->property === '::interface string::',
            ],
        ];
    }

    #[Test]
    #[DataProvider('weirdFormattingObjectInstantiationDataProvider')]
    public function it_instantiates_objects_with_weird_formatting(string $method, Closure $validator): void
    {
        $mock = Mock::class(WeirdFormattingDefaultArgs::class);

        Mock::method($mock->{$method}(...))->replace($validator);

        $this->assertTrue($mock->{$method}());
    }

    public static function weirdFormattingObjectInstantiationDataProvider(): array
    {
        return [
            'Extra spaces around equals' => [
                'methodExtraSpacesAroundEquals',
                fn (MixedConstructor $m) => $m->property === null,
            ],
            'Multiline signature' => [
                'methodMultilineSignature',
                fn (MixedConstructor $m) => $m->property === null,
            ],
            'Multiline signature with string' => [
                'methodMultilineSignatureWithString',
                fn (MixedConstructor $m) => $m->property === '::multiline string::',
            ],
            'Spaces inside constructor args' => [
                'methodSpacesInsideConstructorArgs',
                fn (MixedConstructor $m) => $m->property === '::spaced args string::',
            ],
            'Space between class and opening paren' => [
                'methodSpaceBetweenClassAndParens',
                fn (MixedConstructor $m) => $m->property === '::spaced parens::',
            ],
            'Everything on its own line' => [
                'methodEverythingOnItsOwnLine',
                fn (MixedConstructor $m) => $m->property === '::everything separate::',
            ],
            'Tabs as indentation' => [
                'methodTabsAsIndentation',
                fn (MixedConstructor $m) => $m->property === '::tabs indented::',
            ],
            'Trailing comma in constructor' => [
                'methodTrailingCommaInConstructor',
                fn (MixedConstructor $m) => $m->property === '::trailing comma::',
            ],
            'Comment inside constructor args' => [
                'methodCommentInsideArgs',
                fn (MixedConstructor $m) => $m->property === '::commented args::',
            ],
            'Multiline nested constructors' => [
                'methodMultilineNested',
                fn (MixedConstructor $m) => $m->property instanceof MixedConstructor
                    && $m->property->property === '::multiline nested::',
            ],
            'Comment between new and class name' => [
                'methodCommentBetweenNewAndClass',
                fn (MixedConstructor $m) => $m->property === '::new comment::',
            ],
            'Named argument in constructor' => [
                'methodNamedArg',
                fn (MixedConstructor $m) => $m->property === '::named arg::',
            ],
            'Comment between function keyword and method name' => [
                'methodFunctionKeywordCommented',
                fn (MixedConstructor $m) => $m->property === '::function commented::',
            ],
        ];
    }
}
