<?php

declare(strict_types=1);

namespace Tests\Documentation;

use Exan\Moock\Mock;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\TestInterface;
use Tests\Components\UserService;
use Tests\Components\UserServiceInterface;

#[Page('Mocking a class')]
class BasicMockingTest extends TestCase
{
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

    #[Example(
        'Mocking an interface',
        'Creating a dummy implementation of whatever interface you want'
    )]
    #[Test]
    public function it_can_mock_an_interface(): void
    {
        $mock = Mock::interface(UserServiceInterface::class);

        $this->assertInstanceOf(UserServiceInterface::class, $mock);
    }

    #[Example(
        'Mocking several interfaces',
        'Creating a dummy implementation of several interfaces. You should only use this if your interfaces are compatible.'
    )]
    #[Test]
    public function it_can_mock_several_interfaces_at_once()
    {
        $mock = Mock::interfaces(UserServiceInterface::class, TestInterface::class);

        $this->assertInstanceOf(UserServiceInterface::class, $mock);
        $this->assertInstanceOf(TestInterface::class, $mock);
    }
}
