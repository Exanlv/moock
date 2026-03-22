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
        $this->markTestSkipped();
        return;
        $classes = [
            [
                'class' => new class {
                    public string $myProperty = 'my string';
                },
                'property' => 'my string',
            ],
            [
                'class' => new class {
                    public array $myProperty = ['my string'];
                },
                'property' => ['my string'],
            ],
            [
                'class' => new class {
                    public array $myProperty = ['my string'];
                },
                'property' => ['my string'],
            ],
            [
                'class' => new class {
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
        $this->markTestSkipped();
        return;
        $class = new class {
            public PropertiesTestClass $myProperty;
        };

        $mock = Mock::class($class::class);

        $mock->myProperty = new PropertiesTestClass();

        $this->expectException(TypeError::class);
        $mock->myProperty = 'test';
    }

    #[Test]
    public function it_keeps_readonly()
    { $this->markTestSkipped();
        return;
        $class = new class {
            public readonly string $myProperty;
        };

        $mock = Mock::class($class::class);

        try {
            $mock->myProperty = 'something';
        } catch (Error $e) {
            $this->assertStringContainsString('readonly', $e->getMessage());
        }
    }

    #[Test]
    public function it_forwards_a_property()
    {$this->markTestSkipped();
        return;
        $mock = Mock::class(PropertiesTestClass::class);
        $partial = new class {
            public string $myString = 'my other string';
            public int $myInt = 321;
        };

        Mock::partial($mock, $partial);

        Mock::properties($mock)
            ->forward(
                $mock->myString,
                $mock->myInt,
            );

        $this->assertEquals('my other string', $mock->myString);
        $this->assertEquals(321, $mock->myInt);

        $partial->myString = 'yet another string';
        $partial->myInt = 456;

        $this->assertEquals('yet another string', $mock->myString);
        $this->assertEquals(456, $mock->myInt);
    }

    #[Test]
    public function it_can_forward_properties_on_anonymous_classes()
    {
        $this->markTestSkipped();
        return;
        $class = new class {
            public string $myString = 'my string';
            public int $myInt = 123;
        };

        $mock = Mock::class($class::class);

        $partial = new class {
            public string $myString = 'my other string';
            public int $myInt = 321;
        };

        Mock::partial($mock, $partial);

        Mock::properties($mock)
            ->forward($mock->myString, $mock->myInt);

        $this->assertEquals('my other string', $mock->myString);
        $this->assertEquals(321, $mock->myInt);

        $partial->myString = 'yet another string';
        $partial->myInt = 456;

        $this->assertEquals('yet another string', $mock->myString);
        $this->assertEquals(456, $mock->myInt);
    }
}
