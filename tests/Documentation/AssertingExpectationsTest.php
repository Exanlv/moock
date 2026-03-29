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
    public function it_asserts_method_not_called_at_all()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->not()
            ->toHaveBeenCalled();
    }

    #[Example(null, 'Asserting the method was called at all.')]
    #[Test]
    public function it_asserts_method_was_called()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->toHaveBeenCalled();
    }

    #[Example(null, 'Asserting the method was called exactly once.')]
    #[Test]
    public function it_asserts_method_called_once()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->toHaveBeenCalledOnce();
    }

    #[Example(null, 'Asserting the method was not called exactly once.')]
    #[Test]
    public function it_asserts_method_not_called_once()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('my-email@domain.com');
        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->not()
            ->toHaveBeenCalledOnce();
    }

    #[Example(null, 'Asserting the method was called _n_ times.')]
    #[Test]
    public function it_asserts_method_called_n_times()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('my-email@domain.com');
        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->toHaveBeenCalledTimes(2);
    }

    #[Example(null, 'Asserting the method was not called _n_ times.')]
    #[Test]
    public function it_asserts_method_not_called_n_times()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->not()
            ->toHaveBeenCalledTimes(2);
    }

    #[Example('Asserting method was called with specific input', 'You can assert a method was called with specific input by passing the expected arguments into `with()`.')]
    #[Test]
    public function it_asserts_method_was_called_with_specific_input()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->with('my-email@domain.com')
            ->toHaveBeenCalled();
    }

    #[Example(null, 'This can of course also be reversed.')]
    #[Test]
    public function it_asserts_method_was_called_not_with_specific_input()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->not()
            ->with('other-email@domain.com')
            ->toHaveBeenCalled();
    }

    #[Example(null, 'Rather than being tied to static values, you can pass a closure as well.')]
    #[Test]
    public function it_asserts_method_was_called_with_value_validated_by_closure()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('my-email@domain.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->with(fn (string $email) => str_ends_with($email, '@domain.com'))
            ->toHaveBeenCalled();
    }

    #[Example(null, 'If you only care about a specific argument, you can use named arguments.')]
    #[Test]
    public function it_asserts_method_was_called_with_specific_named_arguments()
    {
        $this->mock->createUser('my-email@domain.com', 'my-username', 'password');

        Mock::method($this->mock->createUser(...))
            ->expect()
            ->with(email: 'my-email@domain.com', password: 'password')
            ->toHaveBeenCalled();
    }

    #[Example(null, 'Of course, closures can be used here too.')]
    #[Test]
    public function it_asserts_method_was_called_with_specific_named_arguments_and_closures()
    {
        $this->mock->createUser('my-email@domain.com', 'my-username', 'password');

        Mock::method($this->mock->createUser(...))
            ->expect()
            ->with(
                email: fn (string $email) => str_ends_with($email, '@domain.com'),
                password: 'password',
            )->toHaveBeenCalled();
    }

    #[Example(null, '`string` must contain `@mail.com`')]
    #[Test]
    public function it_asserts_string_contains_helper()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('test@mail.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->with(Str::contains('@mail.com'))
            ->toHaveBeenCalled();
    }

    #[Example(null, '`string` must have specific length')]
    #[Test]
    public function it_asserts_string_length_helper()
    {
        Mock::method($this->mock->userExists(...))
            ->forceReturn(true);

        $this->mock->userExists('test@mail.com');

        Mock::method($this->mock->userExists(...))
            ->expect()
            ->with(Str::length(13))
            ->toHaveBeenCalled();
    }

    #[Example(null, '`DateTimeInterface` must be before given time')]
    #[Test]
    public function it_asserts_date_before_helper()
    {
        Mock::method($this->mock->getUsersCreatedBefore(...))
            ->forceReturn([]);

        $this->mock->getUsersCreatedBefore(new DateTime('2024-12-31'));

        Mock::method($this->mock->getUsersCreatedBefore(...))
            ->expect()
            ->with(Date::before(new DateTime('2025-01-01 12:00:00')))
            ->toHaveBeenCalled();
    }

    #[Example(null, '`DateTimeInterface` must be after given time')]
    #[Test]
    public function it_asserts_date_after_helper()
    {
        Mock::method($this->mock->getUsersCreatedBefore(...))
            ->forceReturn([]);

        $this->mock->getUsersCreatedBefore(new DateTime('2025-01-02'));

        Mock::method($this->mock->getUsersCreatedBefore(...))
            ->expect()
            ->with(Date::after(new DateTime('2025-01-01 12:00:00')))
            ->toHaveBeenCalled();
    }

    #[Example(null, '`int|float` must be less than given number')]
    #[Test]
    public function it_asserts_number_lt_helper()
    {
        Mock::method($this->mock->getUsersByAge(...))
            ->forceReturn([]);

        $this->mock->getUsersByAge(50);

        Mock::method($this->mock->getUsersByAge(...))
            ->expect()
            ->with(Number::lt(100))
            ->toHaveBeenCalled();
    }

    #[Example(null, '`int|float` must be greater than given number')]
    #[Test]
    public function it_asserts_number_gt_helper()
    {
        Mock::method($this->mock->getUsersByAge(...))
            ->forceReturn([]);

        $this->mock->getUsersByAge(75);

        Mock::method($this->mock->getUsersByAge(...))
            ->expect()
            ->with(Number::gt(50))
            ->toHaveBeenCalled();
    }

    #[Example(null, '`int|float` must be within range')]
    #[Test]
    public function it_asserts_number_range_helper()
    {
        Mock::method($this->mock->getUsersByAge(...))
            ->forceReturn([]);

        $this->mock->getUsersByAge(15);

        Mock::method($this->mock->getUsersByAge(...))
            ->expect()
            ->with(Number::range(10, 20))
            ->toHaveBeenCalled();
    }

    #[Example(null, '`array` must have given number of items')]
    #[Test]
    public function it_asserts_array_count_helper()
    {
        $this->mock->deleteUsersByEmail(['a','b','c']);

        Mock::method($this->mock->deleteUsersByEmail(...))
            ->expect()
            ->with(Arr::count(3))
            ->toHaveBeenCalled();
    }

    #[Example(null, '`array` must be a partial match')]
    #[Test]
    public function it_asserts_array_partial_helper()
    {
        $this->mock->deleteUsersByEmail([
            'some-email@example.com',
            'ignore-this@mail.com',
            'another@example.com',
        ]);

        Mock::method($this->mock->deleteUsersByEmail(...))
            ->expect()
            ->with(Arr::partial([
                0 => 'some-email@example.com',
                2 => fn ($email) => str_ends_with($email, '@example.com'),
            ]))
            ->toHaveBeenCalled();
    }
}
