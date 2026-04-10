<?php

declare(strict_types=1);

namespace Tests\Documentation;

use Exan\Moock\Mock;
use Exan\Moock\MockedClassInterface;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Components\UserServiceInterface;

#[Page('Filtering method arguments', 'Filters restrict which arguments are allowed at runtime. Calls with disallowed input will immediately throw a `RuntimeException`.')]
class FilteringMethodsTest extends TestCase
{
    protected UserServiceInterface&MockedClassInterface $mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock = Mock::interface(UserServiceInterface::class);
    }

    #[Example(
        'Filtering an argument',
        'To filter arguments that are allowed into a method, you can use the following.',
    )]
    #[Test]
    public function it_can_filter_method_args(): void
    {
        Mock::method($this->mock->userExists(...))
            ->allow('my-email@domain.com')
            ->returns(true);

        $this->assertTrue($this->mock->userExists('my-email@domain.com'));

        $this->expectException(RuntimeException::class);
        // Since other-email@domain.com is not allowed per the filter, the method throws a RuntimeException
        $this->mock->userExists('other-email@domain.com');
    }

    #[Example(
        null,
        'To filter specific args of a method, use named properties.',
    )]
    #[Test]
    public function it_can_filter_method_args_using_named_props(): void
    {
        Mock::method($this->mock->createUser(...))
            ->allow(username: 'my-username');

        $this->mock->createUser('my-email@domain.com', 'my-username', 'password');

        $this->expectException(RuntimeException::class);
        // Since username: other-username is not allowed per the filter, the method throws a RuntimeException
        $this->mock->createUser('my-email@domain.com', 'other-username', 'password');
    }

    #[Example(
        'Using closures',
        'You can also pass a closure instead of a straight value, or use some of the helper functions documented in the expectations section instead.',
    )]
    #[Test]
    public function it_can_filter_method_args_with_closures(): void
    {
        Mock::method($this->mock->userExists(...))
            ->allow(fn (string $email) => in_array($email, ['first@mail.com', 'second@mail.com']))
            ->returns(true);

        $this->mock->userExists('first@mail.com');
        $this->mock->userExists('second@mail.com');

        $this->expectException(RuntimeException::class);
        $this->mock->userExists('third@domain.com');
    }
}
