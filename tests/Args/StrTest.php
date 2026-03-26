<?php

declare(strict_types=1);

namespace Tests;

use Exan\Moock\Args\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function test_it_validates_length(): void
    {
        $validator = Str::length(5);

        static::assertTrue($validator('12345'));
        static::assertFalse($validator('1234'));
        static::assertFalse($validator('123456'));
    }

    public function test_it_validates_contains(): void
    {
        $validator = Str::contains('world');

        static::assertTrue($validator('hello world'));
        static::assertTrue($validator('hello WoRLd'));

        static::assertFalse($validator('hello woorld'));
    }
}
