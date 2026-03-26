<?php

declare(strict_types=1);

namespace Tests\Properties;

use Exan\Moock\Properties\Mocker;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Tests\Components\PropertiesTestClass;

class MockerTest extends TestCase
{
    #[Test]
    #[TestWith(['public string $myString = \'my string\''])]
    #[TestWith(['public array $myArray = []'])]
    #[TestWith(['public int $myInt = 123'])]
    #[TestWith(['public bool $myBool = false'])]
    /**
     * ?Type syntax gets converted to Type|null, easier on implementing end and makes no functional difference
     */
    #[TestWith(['public ?string $myString = \'my string\'', 'public string|null $myString = \'my string\''])]
    #[TestWith(['public string|null $myString = \'my string\''])]
    #[TestWith(['public array|null $myArray = []'])]
    #[TestWith(['public int|null $myInt = 123'])]
    #[TestWith(['public bool|null $myBool = false'])]
    #[TestWith(['public string|null $myString = null'])]
    #[TestWith(['public array|null $myArray = null'])]
    #[TestWith(['public int|null $myInt = null'])]
    #[TestWith(['public bool|null $myBool = null'])]
    public function it_mocks_properties(string $property, ?string $expected = null): void
    {
        $expected ??= $property;

        preg_match('/\$(\w+)/', $property, $matches);
        $propertyName = $matches[1];

        $class = eval("return new class { $property; };");

        $mocker = new Mocker($class::class);

        $code = $mocker->getCode();

        static::assertStringStartsWith($expected, $code);
        static::assertStringContainsString($this->getGetHook($propertyName), $code);
        static::assertStringContainsString($this->getSetHook($propertyName), $code);
    }

    #[Test]
    public function it_overwrites_property_hooks(): void
    {
        $class = new class () {
            public string $myString = 'some value' {
                get {
                    return 'some other value';
                }
                set(string $value) {
                    $this->myString = strtolower($value);
                }
            }
        };

        $mocker = new Mocker($class::class);
        $code = $mocker->getCode();

        static::assertStringContainsString($this->getGetHook('myString'), $code);
        static::assertStringContainsString($this->getSetHook('myString'), $code);
    }

    #[Test]
    public function it_does_not_add_hooks_for_readonly_properties(): void
    {
        // Note: PHP limitation, unable to create hooks for these properties

        $class = new class () {
            public readonly string $myString;
        };

        $mocker = new Mocker($class::class);
        $code = $mocker->getCode();

        // Note: Since this property does not have hooks, it MUST have a trailing ';'
        static::assertEquals('public readonly string $myString;', trim($code));
    }

    #[Test]
    public function it_does_not_add_hooks_for_final_readonly_properties(): void
    {
        // Note: PHP limitation, unable to create hooks for these properties

        $class = new class () {
            final public readonly string $myString;
        };

        $mocker = new Mocker($class::class);
        $code = $mocker->getCode();

        // Note: Since this property does not have hooks, it MUST have a trailing ';'
        static::assertEquals('final public readonly string $myString;', trim($code));
    }

    #[Test]
    public function it_mocks_private_set_properties(): void
    {
        // Note: this would only work on properties defined in an anonymous class, as extending a class with these
        // properties would result in an error if attempting to overwrite

        $class = new class () {
            public private(set) string $myString;
        };

        $mocker = new Mocker($class::class);
        $code = $mocker->getCode();

        // private(set) implicitly makes a property final, this is fine to "incorrectly" add here.
        static::assertStringStartsWith('final public private(set) string $myString', $code);

        static::assertStringContainsString($this->getGetHook('myString'), $code);
        static::assertStringContainsString($this->getSetHook('myString'), $code);
    }

    #[Test]
    public function it_mocks_protected_set_properties(): void
    {
        $class = new class () {
            public protected(set) string $myString;
        };

        $mocker = new Mocker($class::class);
        $code = $mocker->getCode();

        static::assertStringStartsWith('public protected(set) string $myString', $code);

        static::assertStringContainsString($this->getGetHook('myString'), $code);
        static::assertStringContainsString($this->getSetHook('myString'), $code);
    }

    #[Test]
    public function it_mocks_final_properties(): void
    {
        $class = new class () {
            final public string $myString;
        };

        $mocker = new Mocker($class::class);
        $code = $mocker->getCode();

        static::assertStringStartsWith('final public string $myString', $code);

        static::assertStringContainsString($this->getGetHook('myString'), $code);
        static::assertStringContainsString($this->getSetHook('myString'), $code);
    }

    #[Test]
    public function it_only_mocks_public_properties(): void
    {
        $class = new class () {
            public string $publicString = 'public-string';
            protected string $protectedString = 'protected-string';
            private string $privateString = 'private-string';
        };

        $mocker = new Mocker($class::class);
        $code = $mocker->getCode();

        static::assertStringContainsString('public-string', $code);
        static::assertStringNotContainsString('protected-string', $code);
        static::assertStringNotContainsString('private-string', $code);
    }

    #[Test]
    public function it_mocks_properties_of_parent_as_well(): void
    {
        $class = new class () extends PropertiesTestClass {
            public string $myString;
        };

        $mocker = new Mocker($class::class);
        $code = $mocker->getCode();

        static::assertStringContainsString('public string $myString', $code);
        static::assertStringContainsString('public protected(set) string $protectedSetString', $code);

        static::assertStringContainsString($this->getGetHook('myString'), $code);
        static::assertStringContainsString($this->getSetHook('myString'), $code);

        static::assertStringContainsString($this->getGetHook('protectedSetString'), $code);
        static::assertStringContainsString($this->getSetHook('protectedSetString'), $code);
    }

    private function getGetHook(string $property): string
    {
        return sprintf('$this->__moockPropertyGet(\'%s\')', $property, $property);
    }

    private function getSetHook(string $property): string
    {
        return sprintf('set { $this->%s = $this->__mockPropertySet(\'%s\', $value); }', $property, $property);
    }
}
