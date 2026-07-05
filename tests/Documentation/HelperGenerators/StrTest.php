<?php

declare(strict_types=1);

namespace Tests\Documentation\HelperGenerators;

use Exan\Moock\Args\Str;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use Exan\Pudocumenter\Attributes\ShowUse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Page('String helpers', null)]
#[ShowUse(Str::class)]
class StrTest extends TestCase
{
    #[Example('Length', 'Validate exact string length')]
    #[Test]
    public function it_validates_length(): void
    {
        $validator = Str::length(5);

        $this->assertTrue($validator('hello'));
        $this->assertFalse($validator('hell'));
    }

    #[Example('Contains', 'Validate substring presence (case-insensitive)')]
    #[Test]
    public function it_validates_contains(): void
    {
        $validator = Str::contains('world');

        $this->assertTrue($validator('hello world'));
        $this->assertTrue($validator('hello WoRLd'));
        $this->assertFalse($validator('hello there'));
    }

    #[Example('Starts With', 'Validate string prefix (case-insensitive)')]
    #[Test]
    public function it_validates_starts_with(): void
    {
        $validator = Str::startsWith('hel');

        $this->assertTrue($validator('hello'));
        $this->assertFalse($validator('world'));
    }

    #[Example('Ends With', 'Validate string suffix (case-insensitive)')]
    #[Test]
    public function it_validates_ends_with(): void
    {
        $validator = Str::endsWith('lo');

        $this->assertTrue($validator('hello'));
        $this->assertFalse($validator('world'));
    }

    #[Example('Regex', 'Validate string against regex pattern')]
    #[Test]
    public function it_validates_regex(): void
    {
        $validator = Str::matchesRegex('/^[a-z]+$/');

        $this->assertTrue($validator('hello'));
        $this->assertFalse($validator('hello123'));
    }

    #[Example('Min Length', 'Validate minimum string length')]
    #[Test]
    public function it_validates_min_length(): void
    {
        $validator = Str::minLength(3);

        $this->assertTrue($validator('abcd'));
        $this->assertFalse($validator('ab'));
    }

    #[Example('Max Length', 'Validate maximum string length')]
    #[Test]
    public function it_validates_max_length(): void
    {
        $validator = Str::maxLength(3);

        $this->assertTrue($validator('ab'));
        $this->assertFalse($validator('abcd'));
    }

    #[Example('Alpha', 'Validate string contains alphabetical characters only')]
    #[Test]
    public function it_validates_alpha(): void
    {
        $validator = Str::alpha();

        $this->assertTrue($validator('abc'));
        $this->assertFalse($validator('abc123'));
    }

    #[Example('Alphanumeric', 'Validate string contains alphaniumeric characters only')]
    #[Test]
    public function it_validates_alphanumeric(): void
    {
        $validator = Str::alphanumeric();

        $this->assertTrue($validator('abc123'));
        $this->assertFalse($validator('abc-123'));
    }

    #[Example('Lowercase', 'Validate string is fully lowercase')]
    #[Test]
    public function it_validates_lowercase(): void
    {
        $validator = Str::lowercase();

        $this->assertTrue($validator('hello'));
        $this->assertFalse($validator('Hello'));
    }

    #[Example('Uppercase', 'Validate string is fully uppercase')]
    #[Test]
    public function it_validates_uppercase(): void
    {
        $validator = Str::uppercase();

        $this->assertTrue($validator('HELLO'));
        $this->assertFalse($validator('Hello'));
    }

    #[Example('Not Empty', 'Validate string is not empty')]
    #[Test]
    public function it_validates_not_empty(): void
    {
        $validator = Str::notEmpty();

        $this->assertTrue($validator('a'));
        $this->assertFalse($validator(''));
    }
}
