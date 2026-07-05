<?php

declare(strict_types=1);

namespace Tests\Documentation;

use DateTime;
use Exan\Moock\Args\Arr;
use Exan\Moock\Args\Date;
use Exan\Moock\Args\Number;
use Exan\Moock\Args\Str;
use Exan\Moock\Mock;
use Exan\Moock\MockedClassInterface;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use Exan\Pudocumenter\Attributes\ShowUse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\UserServiceInterface;

#[Page('Asserting expectations', 'These assertions work out of the box with both [PHPUnit](https://packagist.org/packages/phpunit/phpunit) and [Nette Tester](https://packagist.org/packages/nette/tester). If neither are installed, a regular PHP `assert` is used.')]
class AssertingExpectationsTest extends TestCase
{
    protected UserServiceInterface&MockedClassInterface $mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock = Mock::interface(UserServiceInterface::class);
    }

    #[Example('Asserting amount of calls', 'Asserting the method not called at all.')]
    #[Test]
    public function it_asserts_method_not_called_at_all(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->not()
            ->called();
    }

    #[Example(null, 'Asserting the method was called at all.')]
    #[Test]
    public function it_asserts_method_was_called(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->called();
    }

    #[Example(null, 'Asserting the method was called exactly once.')]
    #[Test]
    public function it_asserts_method_called_once(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->calledOnce();
    }

    #[Example(null, 'Asserting the method was not called exactly once.')]
    #[Test]
    public function it_asserts_method_not_called_once(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('my-email@domain.com');
        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->not()
            ->calledOnce();
    }

    #[Example(null, 'Asserting the method was called _n_ times.')]
    #[Test]
    public function it_asserts_method_called_n_times(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('my-email@domain.com');
        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->calledTimes(2);
    }

    #[Example(null, 'Asserting the method was not called _n_ times.')]
    #[Test]
    public function it_asserts_method_not_called_n_times(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->not()
            ->calledTimes(2);
    }

    #[Example('Asserting method was called with specific input', 'You can assert a method was called with specific input by passing the expected arguments into `with()`.')]
    #[Test]
    public function it_asserts_method_was_called_with_specific_input(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->with('my-email@domain.com')
            ->called();
    }

    #[Example(null, 'This can of course also be reversed.')]
    #[Test]
    public function it_asserts_method_was_called_not_with_specific_input(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->with('other-email@domain.com')
            ->not()
            ->called();
    }

    #[Example(null, 'Rather than being tied to static values, you can pass a closure as well.')]
    #[Test]
    public function it_asserts_method_was_called_with_value_validated_by_closure(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->with(fn (string $email) => str_ends_with($email, '@domain.com'))
            ->called();
    }

    #[Example(null, 'If you only care about a specific argument, you can use named arguments.')]
    #[Test]
    public function it_asserts_method_was_called_with_specific_named_arguments(): void
    {
        $this->mock->createUser('my-email@domain.com', 'my-username', 'password');

        Mock::method($this->mock->createUser(...))
            ->assert()
            ->with(email: 'my-email@domain.com', password: 'password')
            ->called();
    }

    #[Example(null, 'Of course, closures can be used here too.')]
    #[Test]
    public function it_asserts_method_was_called_with_specific_named_arguments_and_closures(): void
    {
        $this->mock->createUser('my-email@domain.com', 'my-username', 'password');

        Mock::method($this->mock->createUser(...))
            ->assert()
            ->with(
                email: fn (string $email) => str_ends_with($email, '@domain.com'),
                password: 'password',
            )->called();
    }

    #[ShowUse(Str::class)]
    #[Example('Built-in helpers', '`string` must contain `@mail.com`')]
    #[Test]
    public function it_asserts_string_contains_helper(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('test@mail.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->with(Str::contains('@mail.com'))
            ->called();
    }

    #[ShowUse(Str::class)]
    #[Example(null, '`string` must have specific length')]
    #[Test]
    public function it_asserts_string_length_helper(): void
    {
        Mock::method($this->mock->userExists(...))
            ->returns(true);

        $this->mock->userExists('test@mail.com');

        Mock::method($this->mock->userExists(...))
            ->assert()
            ->with(Str::length(13))
            ->called();
    }

    #[ShowUse(Number::class)]
    #[Example(null, '`int|float` must be less than given number')]
    #[Test]
    public function it_asserts_number_lt_helper(): void
    {
        Mock::method($this->mock->getUsersByAge(...))
            ->returns([]);

        $this->mock->getUsersByAge(50);

        Mock::method($this->mock->getUsersByAge(...))
            ->assert()
            ->with(Number::lt(100))
            ->called();
    }

    #[ShowUse(Number::class)]
    #[Example(null, '`int|float` must be greater than given number')]
    #[Test]
    public function it_asserts_number_gt_helper(): void
    {
        Mock::method($this->mock->getUsersByAge(...))
            ->returns([]);

        $this->mock->getUsersByAge(75);

        Mock::method($this->mock->getUsersByAge(...))
            ->assert()
            ->with(Number::gt(50))
            ->called();
    }

    #[Example(null, 'To see more of these helpers, head on over to the [argument validators](./arg-validation.md) documentation.')]
    public function redirect_to_arg_validator_doc(): void {}
}
