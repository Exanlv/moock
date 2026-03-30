<?php

declare(strict_types=1);

namespace Tests\Documentation;

use Exan\Moock\Mock;
use Exan\Moock\MoockAssert;
use Exan\Pudocumenter\Attributes\Example;
use Exan\Pudocumenter\Attributes\Page;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Components\UserServiceInterface;

#[Page('Compatibility', 'Out of the box, Moock will use PHPUnit or Nette Tester assertion methods if they\'re available. If neither are available, a regular PHP assert is used instead.')]
#[RunClassInSeparateProcess]
class CompatibilityTest extends TestCase
{
    #[Example('Registering a custom assertion', null)]
    #[Test]
    public function it_can_register_arbitrary_assertion_methods(): void
    {
        $usedAssert = false;

        MoockAssert::useAssert(function (bool $actual, bool $expected, string $message) use (&$usedAssert): void {
            $usedAssert = true;
        });

        $mock = Mock::interface(UserServiceInterface::class);

        Mock::method($mock->createUser(...))
            ->expect()
            ->toHaveBeenCalled(); // This now uses our custom assert, which doesn't throw exceptions on failure

        $this->assertTrue($usedAssert);
    }

    #[Example(null, 'If you like, you can also add optional Moock compatibility from your end by adding the following into an autoloaded file.')]
    public function for_testing_library_devs(): void
    {
        $this->assertTrue(class_exists('\Exan\Moock\MoockAssert')); // @hide
        $handler = function (bool $actual, bool $expected, string $message): void {}; // @hide

        if (class_exists('\Exan\Moock\MoockAssert')) {
            \Exan\Moock\MoockAssert::useAssert($handler);
        }
    }
}
