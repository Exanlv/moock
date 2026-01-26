<?php

declare(strict_types=1);

namespace Tests;

use DateTime;
use Exan\Moock\Args\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    public function test_it_validates_after(): void
    {
        $validator = Date::after(new DateTime('2020-01-01 00:00:00'));

        $this->assertTrue($validator(new DateTime('2021-01-01 00:00:01')));
        $this->assertFalse($validator(new DateTime('2019-12-31 23:59:59')));
    }

    public function test_it_validates_before(): void
    {
        $validator = Date::before(new DateTime('2020-01-01 00:00:00'));

        $this->assertTrue($validator(new DateTime('2019-12-31 23:59:59')));
        $this->assertFalse($validator(new DateTime('2021-01-01 00:00:01')));
    }
}
