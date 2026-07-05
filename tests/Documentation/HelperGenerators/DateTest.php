<?php

declare(strict_types=1);

namespace Tests\Documentation\HelperGenerators;

use Exan\Moock\Args\Date;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use Exan\Pudocumenter\Attributes\ShowUse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Page('Date helpers', null)]
#[ShowUse(Date::class)]
class DateTest extends TestCase
{
    #[Example('Before', 'Validate a date is before a given date')]
    #[Test]
    public function it_validates_before(): void
    {
        $target = new \DateTimeImmutable('2024-01-10');
        $validator = Date::before($target);

        $this->assertTrue($validator(new \DateTimeImmutable('2024-01-09')));
        $this->assertFalse($validator(new \DateTimeImmutable('2024-01-11')));
    }

    #[Example('After', 'Validate a date is after a given date')]
    #[Test]
    public function it_validates_after(): void
    {
        $target = new \DateTimeImmutable('2024-01-10');
        $validator = Date::after($target);

        $this->assertTrue($validator(new \DateTimeImmutable('2024-01-11')));
        $this->assertFalse($validator(new \DateTimeImmutable('2024-01-09')));
    }

    #[Example('Between', 'Validate a date is within a range (inclusive)')]
    #[Test]
    public function it_validates_between(): void
    {
        $start = new \DateTimeImmutable('2024-01-10');
        $end = new \DateTimeImmutable('2024-01-20');

        $validator = Date::between($start, $end);

        $this->assertTrue($validator(new \DateTimeImmutable('2024-01-10')));
        $this->assertTrue($validator(new \DateTimeImmutable('2024-01-15')));
        $this->assertTrue($validator(new \DateTimeImmutable('2024-01-20')));
        $this->assertFalse($validator(new \DateTimeImmutable('2024-01-09')));
        $this->assertFalse($validator(new \DateTimeImmutable('2024-01-21')));
    }

    #[Example('Equal', 'Validate a date equals another date')]
    #[Test]
    public function it_validates_equal(): void
    {
        $target = new \DateTimeImmutable('2024-01-10');

        $validator = Date::equal($target);

        $this->assertTrue($validator(new \DateTimeImmutable('2024-01-10')));
        $this->assertFalse($validator(new \DateTimeImmutable('2024-01-11')));
    }

    #[Example('Same day', 'Validate a date falls on the same calendar day')]
    #[Test]
    public function it_validates_same_day(): void
    {
        $target = new \DateTimeImmutable('2024-01-10 10:00:00');

        $validator = Date::sameDay($target);

        $this->assertTrue($validator(new \DateTimeImmutable('2024-01-10 23:59:59')));
        $this->assertFalse($validator(new \DateTimeImmutable('2024-01-11 00:00:00')));
    }

    #[Example('In past', 'Validate a date is in the past')]
    #[Test]
    public function it_validates_in_past(): void
    {
        $validator = Date::inPast();

        $this->assertTrue($validator(new \DateTimeImmutable('yesterday')));
        $this->assertFalse($validator(new \DateTimeImmutable('tomorrow')));
    }

    #[Example('In future', 'Validate a date is in the future')]
    #[Test]
    public function it_validates_in_future(): void
    {
        $validator = Date::inFuture();

        $this->assertTrue($validator(new \DateTimeImmutable('tomorrow')));
        $this->assertFalse($validator(new \DateTimeImmutable('yesterday')));
    }
}
