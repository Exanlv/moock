<?php

declare(strict_types=1);

namespace Tests\Documentation;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use Exan\Moock\Mock;
use Exan\Moock\MockedClassInterface;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Components\TestDefaultReturns;

#[Page('Default return values', 'If a method is called on a mock, but said method is not replaced and a return value is required, it will return a default value. For basic types, an arbitrary hardcoded value is used. If a class or interface is returned, a mock will be returned.')]
class DefaultReturnValuesTest extends TestCase
{
    #[Test]
    #[Example('Basic types', 'These are some of the more basic return values. These values are picked with some assumptions on what would be least problematic, without throwing exceptions. _Note: you should not rely on the exact values. If you need a value to be something specific, configure the method to do so_')]
    public function it_returns_hardcoded_values_for_basic_types(): void
    {
        $mock = Mock::class(TestDefaultReturns::class); // @hide

        $this->assertEquals(false, $mock->returnBool());
        $this->assertEquals(true, $mock->returnTrue());
        $this->assertEquals(false, $mock->returnFalse());

        $this->assertEquals(123, $mock->returnInt());
        $this->assertEquals(123.456, $mock->returnFloat());

        $this->assertEquals([], $mock->returnArray());
        $this->assertEquals([], $mock->returnIterable());

        // These objects do not have any properties
        $this->assertInstanceOf(stdClass::class, $mock->returnObject());
        $this->assertInstanceOf(stdClass::class, $mock->returnStdClass());

        $this->assertEquals(null, $mock->returnNull());
        $this->assertEquals(null, $mock->returnMixed());

        // Returning static, self, or the declaring class will result in the mock returning itself
        $this->assertEquals($mock, $mock->returnStatic());
        $this->assertEquals($mock, $mock->returnSelf());

        // Non-mocked arbitrary instances are returned
        $this->assertInstanceOf(DateTime::class, $mock->returnDateTime());
        $this->assertInstanceOf(DateTimeImmutable::class, $mock->returnDateTimeImmutable());
        $this->assertInstanceOf(DateInterval::class, $mock->returnDateInterval());
        $this->assertInstanceOf(DatePeriod::class, $mock->returnDatePeriod());
    }

    #[Example('Returning mocked objects', 'If a method has a return type of an object, a mocked instance will be returned. It will return the same mock each time, so it can be used for assertions too.')]
    #[Test]
    public function it_returns_same_mocks_each_time(): void
    {
        $mock = Mock::class(TestDefaultReturns::class); // @hide

        $this->assertInstanceOf(MockedClassInterface::class, $mock->returnUserService());

        $mock->returnUserService()->createUser('my-email@mail.com', 'my-username', 'password');

        Mock::method($mock->returnUserService()->createUser(...))
            ->assert()
            ->calledOnce();
    }
}
