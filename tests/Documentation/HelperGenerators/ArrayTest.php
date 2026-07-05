<?php

declare(strict_types=1);

namespace Tests\Documentation\HelperGenerators;

use Exan\Moock\Args\Arr;
use Exan\Moock\Args\Type;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use Exan\Pudocumenter\Attributes\ShowUse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Page('Array helpers', null)]
#[ShowUse(Arr::class)]
class ArrayTest extends TestCase
{
    #[Example('Count', 'Validate the amount of items in an array')]
    #[Test]
    public function it_can_assert_count(): void
    {
        $count = Arr::count(3);

        $this->assertTrue($count([1, 2, 3]));
        $this->assertFalse($count([1, 2]));
    }

    #[Example('Partial match', 'Validate an array has all specified values.')]
    #[Test]
    public function it_can_assert_partial(): void
    {
        $partial = Arr::partial(['key' => 'value']);

        $matching = [
            'key' => 'value',
            'extra-field' => 'extra-value',
        ];

        $missingKey = [
            'other-key' => 'other-value',
        ];

        $this->assertTrue($partial($matching));
        $this->assertFalse($partial($missingKey));
    }

    #[Example('All', 'Validate that all items in an array match a condition.')]
    #[Test]
    public function it_validates_all_items(): void
    {
        $validator = Arr::all(fn (mixed $value) => $value % 2 === 0);

        $this->assertTrue($validator([2, 4, 6]));
        $this->assertFalse($validator([2, 4, 6, 7]));
    }

    #[Example(null, 'This can also be combined with other helpers.')]
    #[Test]
    public function it_validates_all_items_with_type(): void
    {
        $validator = Arr::all(Type::int());

        $this->assertTrue($validator([2, 4, 6]));
        $this->assertFalse($validator([2, '4', 6]));
    }

    #[Example('Any', 'Validate that at least one item matches a condition.')]
    #[Test]
    public function it_can_assert_any(): void
    {
        $validator = Arr::any(fn (mixed $value) => $value === 3);

        $this->assertTrue($validator([1, 2, 3]));
        $this->assertFalse($validator([1, 2, 4]));
    }

    #[Example('None', 'Validate that no items match a condition.')]
    #[Test]
    public function it_can_assert_none(): void
    {
        $validator = Arr::none(fn (mixed $value) => $value === 5);

        $this->assertTrue($validator([1, 2, 3]));
        $this->assertFalse($validator([1, 5, 3]));
    }

    #[Example('Contains', 'Validate an array contains a value')]
    #[Test]
    public function it_can_assert_contains(): void
    {
        $validator = Arr::contains(2);

        $this->assertTrue($validator([1, 2, 3]));
        $this->assertFalse($validator([1, 3, 4]));
    }

    #[Example('Keys', 'Validate an array has specific keys')]
    #[Test]
    public function it_can_assert_keys(): void
    {
        $validator = Arr::keys(['a', 'b']);

        $this->assertTrue($validator(['a' => 1, 'b' => 2, 'c' => 3]));
        $this->assertFalse($validator(['a' => 1, 'c' => 3]));
    }

    #[Example('Empty', 'Validate an array is empty')]
    #[Test]
    public function it_can_assert_empty(): void
    {
        $validator = Arr::empty();

        $this->assertTrue($validator([]));
        $this->assertFalse($validator([1]));
    }

    #[Example('Not empty', 'Validate an array is not empty')]
    #[Test]
    public function it_can_assert_not_empty(): void
    {
        $validator = Arr::notEmpty();

        $this->assertTrue($validator([1]));
        $this->assertFalse($validator([]));
    }

    #[Example('Indexed', 'Validate an array is sequential indexed')]
    #[Test]
    public function it_can_assert_indexed(): void
    {
        $validator = Arr::indexed();

        $this->assertTrue($validator([1, 2, 3]));
        $this->assertFalse($validator(['key-1' => 'a', 'key-2' => 'b']));
    }
}
