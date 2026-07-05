<?php

declare(strict_types=1);

namespace Tests\Documentation\HelperGenerators;

use DateTime;
use Exan\Moock\Args\Type;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use Exan\Pudocumenter\Attributes\ShowUse;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Page('Type helpers', null)]
#[ShowUse(Type::class)]
class TypeTest extends TestCase
{
    #[Example('String', 'Validate that a value is a string')]
    #[Test]
    public function it_validates_string(): void
    {
        $validator = Type::string();

        $this->assertTrue($validator('hello'));
        $this->assertFalse($validator(123));
    }

    #[Example('Integer', 'Validate that a value is an integer')]
    #[Test]
    public function it_validates_int(): void
    {
        $validator = Type::int();

        $this->assertTrue($validator(123));
        $this->assertFalse($validator('123'));
    }

    #[Example('Float', 'Validate that a value is a float')]
    #[Test]
    public function it_validates_float(): void
    {
        $validator = Type::float();

        $this->assertTrue($validator(1.23));
        $this->assertFalse($validator(123));
    }

    #[Example('Array', 'Validate that a value is an array')]
    #[Test]
    public function it_validates_array(): void
    {
        $validator = Type::array();

        $this->assertTrue($validator(['a']));
        $this->assertFalse($validator('a'));
    }

    #[Example('Resource', 'Validate that a value is a resource')]
    #[Test]
    public function it_validates_resource(): void
    {
        $validator = Type::resource();

        $resource = fopen('php://memory', 'r');

        $this->assertTrue($validator($resource));
        $this->assertFalse($validator('not a resource'));

        fclose($resource);
    }

    #[Example('Object', 'Validate that a value is an object')]
    #[Test]
    public function it_validates_object(): void
    {
        $validator = Type::object();

        $this->assertTrue($validator(new \stdClass()));
        $this->assertFalse($validator('not an object'));
    }

    #[Example('Boolean', 'Validate that a value is a boolean')]
    #[Test]
    public function it_validates_bool(): void
    {
        $validator = Type::bool();

        $this->assertTrue($validator(true));
        $this->assertFalse($validator(1));
    }

    #[Example('Null', 'Validate that a value is null')]
    #[Test]
    public function it_validates_null(): void
    {
        $validator = Type::null();

        $this->assertTrue($validator(null));
        $this->assertFalse($validator(false));
    }

    #[Example('Callable', 'Validate that a value is callable')]
    #[Test]
    public function it_validates_callable(): void
    {
        $validator = Type::callable();

        $this->assertTrue($validator(fn () => null));
        $this->assertFalse($validator('not a callable'));
    }

    #[Example('Iterable', 'Validate that a value is iterable')]
    #[Test]
    public function it_validates_iterable(): void
    {
        $validator = Type::iterable();

        $this->assertTrue($validator([1, 2, 3]));
        $this->assertFalse($validator(123));
    }

    #[Example('Scalar', 'Validate that a value is scalar')]
    #[Test]
    public function it_validates_scalar(): void
    {
        $validator = Type::scalar();

        $this->assertTrue($validator('string'));
        $this->assertFalse($validator([1, 2, 3]));
    }

    #[Example('Instance of', 'Validate that a value is instance of a class')]
    #[Test]
    public function it_validates_instance_of(): void
    {
        $validator = Type::instanceOf(Exception::class);

        $this->assertTrue($validator(new Exception()));
        $this->assertTrue($validator(new RuntimeException()));

        $this->assertFalse($validator(new DateTime()));
    }
}
