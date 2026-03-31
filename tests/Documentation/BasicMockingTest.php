<?php

declare(strict_types=1);

namespace Tests\Documentation;

use Exan\Moock\Mock;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use Exan\Pudocumenter\Attributes\ShowUse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\TestInterface;
use Tests\Components\UserService;
use Tests\Components\UserServiceInterface;

#[Page('Creating a mock', '_Note: all configuration of a mock is stored in `$mock`, the static `Mock::...` methods are there to provide the user-facing API._')]
class BasicMockingTest extends TestCase
{
    #[ShowUse(Mock::class)]
    #[Example(
        'Mocking a class',
        'Creating a test dummy of whatever class you want',
    )]
    #[Test]
    public function it_can_mock_a_class(): void
    {
        $mock = Mock::class(UserService::class);

        $this->assertInstanceOf(UserService::class, $mock);
    }

    #[ShowUse(Mock::class)]
    #[Example(
        'Mocking an interface',
        'Creating a dummy implementation of whatever interface you want',
    )]
    #[Test]
    public function it_can_mock_an_interface(): void
    {
        $mock = Mock::interface(UserServiceInterface::class);

        $this->assertInstanceOf(UserServiceInterface::class, $mock);
    }

    #[ShowUse(Mock::class)]
    #[Example(
        'Mocking several interfaces',
        'Creating a mock implementation of several interfaces. Only use when interfaces are compatible to avoid unexpected behavior.',
    )]
    #[Test]
    public function it_can_mock_several_interfaces_at_once(): void
    {
        $mock = Mock::interfaces(UserServiceInterface::class, TestInterface::class);

        $this->assertInstanceOf(UserServiceInterface::class, $mock);
        $this->assertInstanceOf(TestInterface::class, $mock);
    }
}
