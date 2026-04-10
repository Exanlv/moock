<?php

declare(strict_types=1);

namespace Tests;

use Closure;
use Exan\Moock\Mock;
use Exan\Moock\MoockAssert;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\UserServiceInterface;

#[RunTestsInSeparateProcesses]
class MockExpectTest extends TestCase
{
    #[Test]
    public function it_can_expect_order_of_methods_called_on_same_mock(): void
    {
        $success = false;

        MoockAssert::useAssert(function (bool $condition, bool $expectation, string $description) use (&$success): void {
            $success = $condition && $expectation;
        });

        $mock = Mock::interface(UserServiceInterface::class);

        Mock::method($mock->getUsersByAge(...))->returns([]);

        $mock->createUser('a', 'b', 'c');
        $mock->getUsersByAge(123);
        $mock->createUser('a', 'b', 'c');
        $mock->createUser('a', 'b', 'c');
        $mock->getUsersByAge(123);

        Mock::verify(function (Closure $expect) use ($mock): void {
            $expect($mock->createUser(...));
            $expect($mock->getUsersByAge(...));
            $expect($mock->createUser(...));
            $expect($mock->createUser(...));
            $expect($mock->getUsersByAge(...));
        });

        $this->assertTrue($success);

        $mock = Mock::interface(UserServiceInterface::class);

        Mock::method($mock->getUsersByAge(...))->returns([]);

        $mock->createUser('a', 'b', 'c');
        $mock->getUsersByAge(123);
        $mock->createUser('a', 'b', 'c');
        $mock->createUser('a', 'b', 'c');
        $mock->getUsersByAge(123);

        Mock::verify(function (Closure $expect) use ($mock): void {
            $expect($mock->getUsersByAge(...));
            $expect($mock->createUser(...));
            $expect($mock->createUser(...));
            $expect($mock->createUser(...));
            $expect($mock->getUsersByAge(...));
        });

        $this->assertFalse($success);
    }

    #[Test]
    public function it_can_expect_order_of_methods_called_on_several_mocks(): void
    {
        $success = false;

        MoockAssert::useAssert(function (bool $condition, bool $expectation, string $description) use (&$success): void {
            $success = $condition && $expectation;
        });

        [$mock1, $mock2, $mock3] = array_map(
            fn () => Mock::interface(UserServiceInterface::class),
            range(0, 2)
        );

        $mock1->createUser('a', 'b', 'c');
        $mock2->createUser('a', 'b', 'c');
        $mock3->createUser('a', 'b', 'c');
        $mock3->createUser('a', 'b', 'c');
        $mock2->createUser('a', 'b', 'c');
        $mock1->createUser('a', 'b', 'c');

        Mock::verify(function (Closure $expect) use ($mock1, $mock2, $mock3): void {
            $expect($mock1->createUser(...));
            $expect($mock2->createUser(...));
            $expect($mock3->createUser(...));
            $expect($mock3->createUser(...));
            $expect($mock2->createUser(...));
            $expect($mock1->createUser(...));
        });

        $this->assertTrue($success);

        [$mock1, $mock2, $mock3] = array_map(
            fn () => Mock::interface(UserServiceInterface::class),
            range(0, 2)
        );

        $mock1->createUser('a', 'b', 'c');
        $mock2->createUser('a', 'b', 'c');
        $mock3->createUser('a', 'b', 'c');
        $mock3->createUser('a', 'b', 'c');
        $mock2->createUser('a', 'b', 'c');
        $mock1->createUser('a', 'b', 'c');

        Mock::verify(function (Closure $expect) use ($mock1, $mock2, $mock3): void {
            $expect($mock1->createUser(...));
            $expect($mock2->createUser(...));
            $expect($mock3->createUser(...));
            $expect($mock3->createUser(...));
            $expect($mock1->createUser(...));
            $expect($mock2->createUser(...));
        });

        $this->assertFalse($success);
    }

    #[Test]
    public function it_can_expect_methods_with_args_in_specific_order(): void
    {
        $success = false;

        MoockAssert::useAssert(function (bool $condition, bool $expectation, string $description) use (&$success): void {
            $success = $condition && $expectation;
        });

        $mock = Mock::interface(UserServiceInterface::class);

        $mock->createUser('a', 'b', 'c');
        $mock->createUser('d', 'e', 'f');
        $mock->createUser('g', 'h', 'i');

        Mock::verify(function (Closure $expect) use ($mock): void {
            $expect($mock->createUser(...))->with('a', 'b', 'c');
            $expect($mock->createUser(...))->with('d', 'e', 'f');
            $expect($mock->createUser(...))->with('g', 'h', 'i');
        });

        $this->assertTrue($success);

        $mock = Mock::interface(UserServiceInterface::class);

        $mock->createUser('a', 'b', 'c');
        $mock->createUser('g', 'h', 'i');
        $mock->createUser('d', 'e', 'f');

        Mock::verify(function (Closure $expect) use ($mock): void {
            $expect($mock->createUser(...))->with('a', 'b', 'c');
            $expect($mock->createUser(...))->with('d', 'e', 'f');
            $expect($mock->createUser(...))->with('g', 'h', 'i');
        });

        $this->assertFalse($success);
    }

    #[Test]
    public function it_ignores_random_calls_in_between(): void
    {
        $success = false;

        MoockAssert::useAssert(function (bool $condition, bool $expectation, string $description) use (&$success): void {
            $success = $condition && $expectation;
        });

        $mock = Mock::interface(UserServiceInterface::class);

        Mock::method($mock->getUsersByAge(...))->returns([]);

        $mock->getUsersByAge(123);
        $mock->createUser('a', 'b', 'c');
        $mock->createUser('j', 'k', 'l');
        $mock->getUsersByAge(123);
        $mock->getUsersByAge(123);
        $mock->getUsersByAge(123);
        $mock->createUser('d', 'e', 'f');
        $mock->getUsersByAge(123);
        $mock->getUsersByAge(123);
        $mock->createUser('g', 'h', 'i');

        Mock::verify(function (Closure $expect) use ($mock): void {
            $expect($mock->createUser(...))->with('a', 'b', 'c');
            $expect($mock->createUser(...))->with('d', 'e', 'f');
            $expect($mock->createUser(...))->with('g', 'h', 'i');
        });

        $this->assertTrue($success);
    }

    #[Test]
    public function it_can_expect_arg_and_non_arg_specific(): void
    {
        $success = false;

        MoockAssert::useAssert(function (bool $condition, bool $expectation, string $description) use (&$success): void {
            $success = $condition && $expectation;
        });

        $mock = Mock::interface(UserServiceInterface::class);

        $mock->createUser('a', 'b', 'c');
        $mock->createUser('d', 'e', 'f');
        $mock->createUser('g', 'h', 'i');

        Mock::verify(function (Closure $expect) use ($mock): void {
            $expect($mock->createUser(...))->with('a', 'b', 'c');
            $expect($mock->createUser(...));
            $expect($mock->createUser(...))->with('g', 'h', 'i');
        });

        $this->assertTrue($success);

        $mock = Mock::interface(UserServiceInterface::class);

        $mock->createUser('a', 'b', 'c');
        $mock->createUser('g', 'h', 'i');
        $mock->createUser('d', 'e', 'f');

        Mock::verify(function (Closure $expect) use ($mock): void {
            $expect($mock->createUser(...))->with('a', 'b', 'c');
            $expect($mock->createUser(...))->with('d', 'e', 'f');
            $expect($mock->createUser(...));
        });

        $this->assertFalse($success);
    }

    #[Test]
    public function it_can_expect_arg_with_fancy_syntax(): void
    {
        $success = false;

        MoockAssert::useAssert(function (bool $condition, bool $expectation, string $description) use (&$success): void {
            $success = $condition && $expectation;
        });

        $mock = Mock::interface(UserServiceInterface::class);

        $mock->createUser('a', 'b', 'c');
        $mock->createUser('a', 'c', 'b');
        $mock->createUser('d', 'e', 'f');
        $mock->createUser('g', 'h', 'i');

        Mock::verify(function (Closure $expect) use ($mock): void {
            $expect($mock->createUser(...))->with(email: 'a', password: 'c');
            $expect($mock->createUser(...))->with(email: 'd', password: 'f');
        });

        $this->assertTrue($success);

        $mock = Mock::interface(UserServiceInterface::class);

        $mock->createUser('a', 'b', 'c');
        $mock->createUser('a', 'c', 'b');
        $mock->createUser('d', 'f', 'e');

        Mock::verify(function (Closure $expect) use ($mock): void {
            $expect($mock->createUser(...))->with(email: 'a', password: 'c');
            $expect($mock->createUser(...))->with(email: 'd', password: 'f');
        });

        $this->assertFalse($success);
    }
}
