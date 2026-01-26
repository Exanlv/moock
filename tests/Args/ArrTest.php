<?php

declare(strict_types=1);

namespace Tests;

use Exan\Moock\Args\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    public function test_it_validates_count(): void
    {
        $validator = Arr::count(3);

        $this->assertTrue($validator([1, 2, 3]));
        $this->assertFalse($validator([1, 2, 3, 4]));
        $this->assertFalse($validator([1, 2]));
    }

    public function test_it_validates_partial_arrays(): void
    {
        $validator = Arr::partial([
            'key' => 'value',
            'multi' => [
                'level' => [
                    'array' => 'other-value',
                ],
            ],
        ]);

        $this->assertTrue($validator([
            'key' => 'value',
            'second-key' => 'another-value',
            'multi' => [
                'level' => [
                    'array' => 'other-value',
                ],
                'additional-key' => 'some-value'
            ],
        ]));

        $this->assertFalse($validator([
            'key' => 'non matching value',
            'second-key' => 'another-value',
            'multi' => [
                'level' => [
                    'array' => 'other-value',
                ],
                'additional-key' => 'some-value'
            ],
        ]));

        $this->assertFalse($validator([
            // 'key' => 'value', item missing
            'second-key' => 'another-value',
            'multi' => [
                'level' => [
                    'array' => 'other-value',
                ],
                'additional-key' => 'some-value'
            ],
        ]));

        $this->assertFalse($validator([
            'key' => 'value',
            'second-key' => 'another-value',
            'multi' => [
                'level' => [
                    'array' => 'non matching in multi level array',
                ],
                'additional-key' => 'some-value'
            ],
        ]));
    }

    public function test_it_validates_partial_arrays_with_callables(): void
    {
        $validator = Arr::partial([
            'key' => fn (string $value) => $value === 'value',
            'multi' => [
                'level' => [
                    'array' => fn (string $value) => $value === 'other-value',
                ],
            ],
        ]);

        $this->assertTrue($validator([
            'key' => 'value',
            'second-key' => 'another-value',
            'multi' => [
                'level' => [
                    'array' => 'other-value',
                ],
                'additional-key' => 'some-value'
            ],
        ]));

        $this->assertFalse($validator([
            'key' => 'non matching value',
            'second-key' => 'another-value',
            'multi' => [
                'level' => [
                    'array' => 'other-value',
                ],
                'additional-key' => 'some-value'
            ],
        ]));

        $this->assertFalse($validator([
            'key' => 'value',
            'second-key' => 'another-value',
            'multi' => [
                'level' => [
                    'array' => 'non matching in multi level array',
                ],
                'additional-key' => 'some-value'
            ],
        ]));
    }

    public function test_it_validates_arrays_with_callable(): void
    {
        $validator = Arr::partial([
            'key' => fn (string $value) => $value === 'value',
            'multi' => fn (array $value) => $value === [
                'level' => [
                    'array' => 'other-value',
                ],
            ],
        ]);

        $this->assertTrue($validator([
            'key' => 'value',
            'second-key' => 'another-value',
            'multi' => [
                'level' => [
                    'array' => 'other-value',
                ],
            ],
        ]));

        $this->assertFalse($validator([
            'key' => 'value',
            'multi' => [
                'level' => [
                    'array' => 'other-value',
                ],
                'additional-key' => 'some-value'
            ],
        ]));
    }
}
