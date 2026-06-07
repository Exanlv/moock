<?php

declare(strict_types=1);

namespace Tests\Documentation;

use Exan\Moock\Mock;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Page('Mock Closures', 'Closures are often used as callbacks, with Mock Closures you can assert what happens with these like you would for any other class mock')]
class MockFnTest extends TestCase
{
    #[Test]
    #[Example(null, null)]
    public function mocking_closures(): void
    {
        $mockFn = Mock::fn();

        $mockFn();
        $mockFn('first arg', 'second arg');

        Mock::method($mockFn)->assert()->calledTimes(2);
        Mock::method($mockFn)->assert()->with('first arg')->called();
    }

    #[Test]
    #[Example(null, 'Named arguments are also supported')]
    public function mocking_closures_asserting_with_named_args(): void
    {
        $mockFn = Mock::fn(); // hide

        $mockFn('first arg', someArg: 'some value');

        Mock::method($mockFn)->assert()->with(someArg: 'some value')->called();
    }

    #[Test]
    #[Example(null, 'Filters can also be applied')]
    public function mocked_closures_accept_filters(): void
    {
        $mockFn = Mock::fn(); // hide

        Mock::method($mockFn)->allow(someArg: 'some value');

        $mockFn('first arg', someArg: 'some value');

        $this->expectException(RuntimeException::class);
        $mockFn('first arg', someArg: 'some other value');
    }
}
