<?php

declare(strict_types=1);

namespace Tests;

use Exan\Moock\Mock;
use Exan\Moock\MockedClassInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Tests\Components\TestDefaultReturns;
use Tests\Components\UserService;
use Tests\Components\UserServiceInterface;
use Throwable;

class MockDefaultReturns extends TestCase
{
    #[Test]
    public function it_returns_valid_defaults(): void
    {
        $mock = Mock::class(TestDefaultReturns::class);

        $ref = new ReflectionClass(TestDefaultReturns::class);

        foreach ($ref->getMethods() as $method) {
            try {
                $mock->{$method->name}();
            } catch (Throwable $e) {
                $this->fail('Unable to retrieve ' . $method->name . ' - ' . $e->getMessage());
            }
        }

        // Not error-ing is enough of a test

        $this->assertTrue(true);
    }

    #[Test]
    public function it_returns_mocks_of_non_bound_classes(): void
    {
        $mock = Mock::class(TestDefaultReturns::class);

        $userServiceMock = $mock->returnUserService();

        $this->assertInstanceOf(MockedClassInterface::class, $userServiceMock);
        $this->assertInstanceOf(UserService::class, $userServiceMock);

        $userServiceInterfaceMock = $mock->returnUserServiceInterface();

        $this->assertInstanceOf(MockedClassInterface::class, $userServiceInterfaceMock);
        $this->assertInstanceOf(UserServiceInterface::class, $userServiceInterfaceMock);
    }
}
