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

#[Page('Replacing methods')]
class ReplacingMethodTest extends TestCase
{
    protected UserServiceInterface&MockedClassInterface $mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock = Mock::interface(UserServiceInterface::class);
    }

    #[Example(
        'Replacing a method',
        'You can replace any public method on your mocks using the following examples',
    )]
    #[Test]
    public function it_can_replace_methods()
    {
        Mock::method($this->mock->userExists(...))->replace(function (string $email) {
            return $email === 'exists@mail.com';
        });

        $this->assertTrue($this->mock->userExists('exists@mail.com'));
        $this->assertFalse($this->mock->userExists('doesnt@mail.com'));
    }

    #[Example(null, 'Force returning a static value')]
    #[Test]
    public function it_can_force_return_a_value()
    {
        Mock::method($this->mock->userExists(...))->forceReturn(true);

        $this->assertTrue($this->mock->userExists('some-email@domain.com'));
    }

    #[Example(null, 'Force returning a sequence of values')]
    #[Test]
    public function it_can_force_return_a_sequence_of_values()
    {
        Mock::method($this->mock->userExists(...))->forceReturnSequence([true, true, false]);

        $this->assertTrue($this->mock->userExists('some-email@domain.com'));
        $this->assertTrue($this->mock->userExists('some-email@domain.com'));

        // 3rd item is false
        $this->assertFalse($this->mock->userExists('some-email@domain.com'));
    }

    #[Example(null, 'Force throwing an exception')]
    #[Test]
    public function it_can_force_an_exception()
    {
        Mock::method($this->mock->createUser(...))->throwsException(RuntimeException::class);

        $this->expectException(RuntimeException::class);

        $this->mock->createUser(
            'mail@domain.com',
            'username',
            'password123',
        );
    }
}
