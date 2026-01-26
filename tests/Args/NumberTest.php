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

        $this->assertTrue($validator(6));
        $this->assertFalse($validator(4));
    }

    public function test_it_validates_lt(): void
    {
        $validator = Number::lt(5);

        $this->assertTrue($validator(4));
        $this->assertFalse($validator(6));
    }

    public function test_it_validates_range(): void
    {
        $validator = Number::range(3, 6);

        $this->assertTrue($validator(3));
        $this->assertTrue($validator(4));
        $this->assertTrue($validator(6));

        $this->assertFalse($validator(7));
        $this->assertFalse($validator(2));
    }
}
