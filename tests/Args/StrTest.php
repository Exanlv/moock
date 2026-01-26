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

        $this->assertTrue($validator('12345'));
        $this->assertFalse($validator('1234'));
        $this->assertFalse($validator('123456'));
    }

    public function test_it_validates_contains(): void
    {
        $validator = Str::contains('world');

        $this->assertTrue($validator('hello world'));
        $this->assertTrue($validator('hello WoRLd'));

        $this->assertFalse($validator('hello woorld'));
    }
}
