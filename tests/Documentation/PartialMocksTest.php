<?php

declare(strict_types=1);

namespace Tests\Documentation;

use Exan\Moock\Mock;
use Exan\Moock\MockedClassInterface;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use Exan\Pudocumenter\Attributes\ShowUse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\UserService;

#[Page('Creating partial mocks')]
class PartialMocksTest extends TestCase
{
    protected UserService $real;
    protected UserService&MockedClassInterface $partial;

    #[Example(
        'Partial mocks',
        "A partial mocks wraps the given object. Any methods and properties will be forwarded and retrieved from its real instance, allowing you to selectively overwrite public behaviour. These can be useful in some scenarios, however, overuse may lead to hard-to-follow tests.",
    )]
    public function setUp(): void
    {
        parent::setUp(); // @hide

        $this->real = new UserService();

        $this->real->users = [
            'first@mail.com',
            'second@mail.com',
            'third@mail.com',
        ];

        $this->partial = Mock::partial($this->real);
    }

    #[Example(null, 'Any method not explicitly mocked will be forwarded to its full implementation.')]
    #[Test]
    public function it_forwards_a_call_to_the_parent(): void
    {
        $this->assertTrue($this->partial->userExists('first@mail.com'));
        $this->assertFalse($this->partial->userExists('fourth@mail.com'));
    }

    #[ShowUse(Mock::class)]
    #[Example(null, 'Methods can still be mocked, in which case the full implementation is bypassed selectively.')]
    #[Test]
    public function it_can_still_mock_methods(): void
    {
        Mock::method($this->partial->userExists(...))
            ->replace(fn (string $email) => $email === 'fourth@mail.com');

        $this->assertFalse($this->partial->userExists('first@mail.com'));
        $this->assertTrue($this->partial->userExists('fourth@mail.com'));
    }

    #[Example(null, 'Properties are also synced between real & fake. _Note: this does not work for properties with `private(set)`, `readonly`, or `final`._')]
    #[Test]
    public function properties_are_forwarded(): void
    {
        $this->assertEquals([
            'first@mail.com',
            'second@mail.com',
            'third@mail.com',
        ], $this->partial->users);

        $this->partial->users = ['fourth@mail.com'];

        $this->assertEquals(['fourth@mail.com'], $this->real->users);
    }
}
