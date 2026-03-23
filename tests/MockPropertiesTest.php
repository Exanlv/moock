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

            $this->assertEquals($expectedValue, $mock->myProperty);
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
    public function it_keeps_readonly()
    {
        $class = new class () {
            public readonly string $myProperty;
        };

        $mock = Mock::class($class::class);

        try {
            $mock->myProperty = 'something';
        } catch (Error $e) {
            $this->assertStringContainsString('readonly', $e->getMessage());
        }
    }
}
