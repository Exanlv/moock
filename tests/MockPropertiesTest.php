<?php

namespace Tests;

use Error;
use Exan\Moock\Mock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\PropertiesTestClass;
use TypeError;

class MockPropertiesTest extends TestCase
{
    #[Test]
    public function it_mocks_public_properties_on_anonymous_classes()
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
    public function it_retains_properties_typings()
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
        $toMock = new class () {
            public string $myFirstProp = '::first string::';
            public string $mySecondProp = '::second string::';
            public string $myThirdProp = '::third string::';
            public string $myFourthProp = '::fourth string::';
        };

        $partial = new class () {
            public string $myFirstProp = '::first string 2::';
            public string $mySecondProp = '::second string 2::';
            public string $myThirdProp = '::third string 2::';
            public string $myFourthProp = '::fourth string 2::';
        };

        $mock = Mock::class($toMock::class);

        static::assertEquals('::first string::', $mock->myFirstProp);
        static::assertEquals('::second string::', $mock->mySecondProp);
        static::assertEquals('::third string::', $mock->myThirdProp);
        static::assertEquals('::fourth string::', $mock->myFourthProp);

        Mock::partial($mock, $partial);

        Mock::properties($mock)->forward(
            $mock->myFirstProp,
            $mock->mySecondProp,
            $mock->myThirdProp,
            $mock->myFourthProp,
        );

        static::assertEquals('::first string 2::', $mock->myFirstProp);
        static::assertEquals('::second string 2::', $mock->mySecondProp);
        static::assertEquals('::third string 2::', $mock->myThirdProp);
        static::assertEquals('::fourth string 2::', $mock->myFourthProp);
    }
}
