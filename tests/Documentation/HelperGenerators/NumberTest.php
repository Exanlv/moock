<?php

declare(strict_types=1);

namespace Tests\Documentation\HelperGenerators;

use Exan\Moock\Args\Number;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use Exan\Pudocumenter\Attributes\ShowUse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Page('Number helpers', null)]
#[ShowUse(Number::class)]
class NumberTest extends TestCase
{
    #[Example('Less than', 'Validate a number is less than a given value')]
    #[Test]
    public function it_validates_lt(): void
    {
        $validator = Number::lt(10);

        $this->assertTrue($validator(5));
        $this->assertFalse($validator(15));
    }

    #[Example('Greater than', 'Validate a number is greater than a given value')]
    #[Test]
    public function it_validates_gt(): void
    {
        $validator = Number::gt(10);

        $this->assertTrue($validator(15));
        $this->assertFalse($validator(5));
    }

    #[Example('Range', 'Validate a number is within a range (inclusive)')]
    #[Test]
    public function it_validates_range(): void
    {
        $validator = Number::range(10, 20);

        $this->assertTrue($validator(10));
        $this->assertTrue($validator(15));
        $this->assertTrue($validator(20));

        $this->assertFalse($validator(9));
        $this->assertFalse($validator(21));
    }

    #[Example('Positive', 'Validate a number is positive')]
    #[Test]
    public function it_validates_positive(): void
    {
        $validator = Number::positive();

        $this->assertTrue($validator(1));
        $this->assertFalse($validator(0));
        $this->assertFalse($validator(-1));
    }

    #[Example('Negative', 'Validate a number is negative')]
    #[Test]
    public function it_validates_negative(): void
    {
        $validator = Number::negative();

        $this->assertTrue($validator(-1));
        $this->assertFalse($validator(0));
        $this->assertFalse($validator(1));
    }

    #[Example('Even', 'Validate a number is even integer')]
    #[Test]
    public function it_validates_even(): void
    {
        $validator = Number::even();

        $this->assertTrue($validator(2));
        $this->assertFalse($validator(3));
        $this->assertFalse($validator(2.5));
    }

    #[Example('Odd', 'Validate a number is odd integer')]
    #[Test]
    public function it_validates_odd(): void
    {
        $validator = Number::odd();

        $this->assertTrue($validator(3));
        $this->assertFalse($validator(2));
        $this->assertFalse($validator(3.5));
    }

    #[Example('Divisible by', 'Validate a number is divisible by a given divisor')]
    #[Test]
    public function it_validates_divisible_by(): void
    {
        $validator = Number::divisibleBy(3);

        $this->assertTrue($validator(9));
        $this->assertFalse($validator(10));
    }

    #[Example('Approximately', 'Validate a number is approximately equal within epsilon')]
    #[Test]
    public function it_validates_approx(): void
    {
        $validator = Number::approx(10.0, 0.01);

        $this->assertTrue($validator(10.005));
        $this->assertFalse($validator(10.1));
    }
}
