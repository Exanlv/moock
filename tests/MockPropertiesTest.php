<?php

declare(strict_types=1);

namespace Tests;

use Error;
use Exan\Moock\Mock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\PropertiesTestClass;
use Tests\Components\PropertiesGetterTestClass;
use TypeError;

class MockPropertiesTest extends TestCase
{
    #[Test]
    public function it_mocks_public_properties_on_anonymous_classes(): void
    {
        $classes = [
            [
                'class' => new class () {
                    public string $myProperty = 'my string';
                },
                'property' => 'my string',
            ],
            [
                'class' => new class () {
                    public array $myProperty = ['my string'];
                },
                'property' => ['my string'],
            ],
            [
                'class' => new class () {
                    public array $myProperty = ['my string'];
                },
                'property' => ['my string'],
            ],
            [
                'class' => new class () {
                    public ?PropertiesTestClass $myProperty = null;
                },
                'property' => null,
            ],
        ];

        foreach ($classes as $case) {
            ['class' => $class, 'property' => $expectedValue] = $case;

            $mock = Mock::class($class::class);

            static::assertEquals($expectedValue, $mock->myProperty);
        }
    }

    #[Test]
    public function it_retains_properties_typings(): void
    {
        $class = new class () {
            public PropertiesTestClass $myProperty;
        };

        $mock = Mock::class($class::class);

        $mock->myProperty = new PropertiesTestClass();

        $this->expectException(TypeError::class);
        $mock->myProperty = 'test';
    }

    #[Test]
    public function it_keeps_readonly(): void
    {
        $class = new class () {
            public readonly string $myProperty;
        };

        $mock = Mock::class($class::class);

        try {
            $mock->myProperty = 'something';
        } catch (Error $e) {
            static::assertStringContainsString('readonly', $e->getMessage());
        }
    }

    #[Test]
    public function it_keeps_track_of_accessed_properties(): void
    {
        $class = new class () {
            public string $myFirstProp = 'test';
            public string $mySecondProp = 'test';
        };

        $mock = Mock::class($class::class);

        $mock->mySecondProp;
        $mock->myFirstProp;
        $mock->myFirstProp;
        $mock->mySecondProp;
        $mock->myFirstProp;
        $mock->myFirstProp;
        $mock->myFirstProp;
        $mock->mySecondProp;

        static::assertEquals([
            'mySecondProp',
            'myFirstProp',
            'myFirstProp',
            'mySecondProp',
            'myFirstProp',
            'myFirstProp',
            'myFirstProp',
            'mySecondProp',
        ], $mock->__getAccessedProperties());
    }

    #[Test]
    public function it_forwards_props(): void
    {
        $toMock = new PropertiesTestClass();

        $toMock->myFirstProp = '::first string 2::';
        $toMock->mySecondProp = '::second string 2::';
        $toMock->myThirdProp = '::third string 2::';
        $toMock->myFourthProp = '::fourth string 2::';

        $mock = Mock::partial($toMock);

        static::assertEquals('::first string 2::', $mock->myFirstProp);
        static::assertEquals('::second string 2::', $mock->mySecondProp);
        static::assertEquals('::third string 2::', $mock->myThirdProp);
        static::assertEquals('::fourth string 2::', $mock->myFourthProp);
    }

    #[Test]
    public function it_forwards_magic_props(): void
    {
        $instance = new PropertiesGetterTestClass(['test' => 'value']);

        $mock = Mock::partial($instance);

        static::assertEquals('value', $mock->test);
    }
}
