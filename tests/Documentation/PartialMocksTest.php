<?php

declare(strict_types=1);

namespace Tests\Documentation;

use Exan\Moock\Mock;
use Exan\Moock\MockedClassInterface;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\UserService;

#[Page('Partial mocks')]
class PartialMocksTest extends TestCase
{
    protected UserService&MockedClassInterface $partial;

    #[Example(
        'Partial mocks',
        'Creating a partial mock can be done in the following way. A partial mock will automatically forward any method call or property get to the partial object.'
    )]
    public function setUp(): void
    {
        parent::setUp(); // @hide

        $userService = new UserService();

        $userService->users = [
            'first@mail.com',
            'second@mail.com',
            'third@mail.com',
        ];

        $this->partial = Mock::partial($userService);
    }

    #[Example(null, 'Any method not explicitly mocked will be forwarded to it\'s full implementation.')]
    #[Test]
    public function it_forwards_a_call_to_the_parent()
    {
        $this->assertTrue($this->partial->userExists('first@mail.com'));
        $this->assertFalse($this->partial->userExists('fourth@mail.com'));
    }

    #[Example(null, 'Methods can still be mocked, in which case the full implementation is bypassed selectively.')]
    #[Test]
    public function it_can_still_mock_methods()
    {
        Mock::method($this->partial->userExists(...))
            ->replace(fn (string $email) => $email === 'fourth@mail.com');

        $this->assertFalse($this->partial->userExists('first@mail.com'));
        $this->assertTrue($this->partial->userExists('fourth@mail.com'));
    }

    #[Example(null, 'Properties are also retrieved from the full implementation. _Note: setting of properties is not yet supported._')]
    #[Test]
    public function properties_are_forwarded()
    {
        $this->assertEquals([
            'first@mail.com',
            'second@mail.com',
            'third@mail.com',
        ], $this->partial->users);
    }
}
