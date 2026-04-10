<?php

declare(strict_types=1);

namespace Tests\Documentation;

use Closure;
use Exan\Moock\Mock;
use Exan\Moock\MockedClassInterface;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\ProductServiceInterface;
use Tests\Components\UserServiceInterface;

#[Page(
    'Order expectations',
    'If you\'re expecting many calls to be made after each other in specific order, you may use `Mock::expect`'
)]
class OrderExpectationTest extends TestCase
{
    private UserServiceInterface&MockedClassInterface $mock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock = Mock::interface(UserServiceInterface::class);
    }

    #[Example('Verifying order of calls', 'To verify the order in which methods were called on a mock, call `$expect` in the desired order with the given method.')]
    #[Test]
    public function it_can_verify_order_of_mocked_methods(): void
    {
        Mock::method($this->mock->isValidEmail(...))->forceReturn(true);
        Mock::method($this->mock->userExists(...))->forceReturn(false);

        $this->mock->isValidEmail('mail@domain.com');
        $this->mock->userExists('mail@domain.com');
        $this->mock->createUser('mail@domain.com', 'username', 'password');

        // Asserting the methods are called in the right order only
        Mock::expect(function ($expect): void {
            $expect($this->mock->isValidEmail(...));
            $expect($this->mock->userExists(...));
            $expect($this->mock->createUser(...));
        });
    }

    #[Example('Verifying order and arguments', 'Optionally attach expectations of the given arguments.')]
    #[Test]
    public function it_can_verify_order_of_mocked_methods_and_args(): void
    {
        Mock::method($this->mock->isValidEmail(...))->forceReturn(true); // @hide
        Mock::method($this->mock->userExists(...))->forceReturn(false); // @hide

        $this->mock->isValidEmail('mail@domain.com'); // @hide
        $this->mock->userExists('mail@domain.com'); // @hide
        $this->mock->createUser('mail@domain.com', 'username', 'password'); // @hide

        Mock::expect(function (Closure $expect): void {
            $expect($this->mock->isValidEmail(...))->with('mail@domain.com');
            $expect($this->mock->userExists(...)); // Argument isn't verified, just order

            // Only mail and password are validated
            $expect($this->mock->createUser(...))->with('mail@domain.com', password: 'password');
        });
    }

    #[Example('Verifying order between different mocks', 'You may also validate in what order methods were called between several mocks')]
    #[Test]
    public function it_can_verify_order_between_different_mocks(): void
    {
        $userService = Mock::interface(UserServiceInterface::class);
        $productsService = Mock::interface(ProductServiceInterface::class);

        Mock::method($userService->isValidEmail(...))->forceReturn(true); // @hide
        Mock::method($productsService->productExists(...))->forceReturn(true); // @hide
        Mock::method($productsService->purchase(...))->forceReturn(true); // @hide

        $productsService->productExists(123);
        $userService->isValidEmail('mail@domain.com');
        $productsService->purchase(123, 'mail@domain.com');

        Mock::expect(function (Closure $expect) use ($userService, $productsService): void {
            $expect($productsService->productExists(...))->with(123);
            $expect($userService->isValidEmail(...))->with('mail@domain.com');
            $expect($productsService->purchase(...))->with(123, 'mail@domain.com');
        });
    }
}
