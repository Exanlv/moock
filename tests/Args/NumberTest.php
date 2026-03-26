<?php

declare(strict_types=1);

namespace Tests;

use Exan\Moock\Args\Number;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    public function test_it_validates_gt(): void
    {
        $validator = Number::gt(5);

        static::assertTrue($validator(6));
        static::assertFalse($validator(4));
    }

    public function test_it_validates_lt(): void
    {
        $validator = Number::lt(5);

        static::assertTrue($validator(4));
        static::assertFalse($validator(6));
    }

    public function test_it_validates_range(): void
    {
        $validator = Number::range(3, 6);

        static::assertTrue($validator(3));
        static::assertTrue($validator(4));
        static::assertTrue($validator(6));

        static::assertFalse($validator(7));
        static::assertFalse($validator(2));
    }
}
