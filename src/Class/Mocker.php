<?php

declare(strict_types=1);

namespace Exan\Moock\Class;

use Exan\Moock\Methods\Mocker as MethodsMocker;
use Exan\Moock\MockedClass;
use Exan\Moock\MockedClassInterface;
use Exan\Moock\Properties\Mocker as PropertiesMocker;
use RuntimeException;

/**
 * @internal
 */
class Mocker
{
    public private(set) array $interfaces = [];
    public private(set) string $extends;

    public function getCode(): string
    {
        assert(!empty($this->interfaces), 'Nothing to mock');

        $implements = array_map(
            fn (string $interface) => '\\' . $interface,
            array_filter(
                $this->interfaces,
                fn (string $interface) => interface_exists($interface),
            ),
        );

        $implements[] = '\\' . MockedClassInterface::class;

        $properties = $this->getPropertyMocker()?->getCode() ?? '// Interface only mock, no properties';
        $methods = (new MethodsMocker($this->interfaces))->getCode();

        $creator = 'return new class ';

        if (isset($this->extends)) {
            $parent = str_contains($this->extends, '@anonymous')
                ? get_parent_class($this->extends)
                : $this->extends;

            if ($parent !== false) {
                $creator .= 'extends \\' . $parent . ' ';
            }
        }

        $creator .= 'implements ' . implode(', ', $implements);
        $creator .= '{' . PHP_EOL;

        $creator .= 'use \\' . MockedClass::class . ';' . PHP_EOL . PHP_EOL;

        $creator .= '// Start properties' . PHP_EOL;
        $creator .= $properties . PHP_EOL . PHP_EOL;
        $creator .= '// End properties' . PHP_EOL . PHP_EOL;

        $creator .= 'public function __construct() { }' . PHP_EOL . PHP_EOL;

        $creator .= '// Start methods' . PHP_EOL;
        $creator .= $methods;
        $creator .= '// End methods' . PHP_EOL . PHP_EOL;

        $creator .= PHP_EOL . '};';

        return $creator;
    }

    public function extends(string $class): static
    {
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('Class "%s" does not exist', $class));
        }

        $this->addClass($class);

        $this->extends = $class;

        return $this;
    }

    public function addClass(string $class): static
    {
        // Class & interface are functionally the same in this usecase, this function is essentially a JIC for future-proofing
        return $this->addInterface($class);
    }

    public function addInterface(string $interface): static
    {
        if (!interface_exists($interface) && !class_exists($interface)) {
            throw new RuntimeException(sprintf('Class or interface "%s" does not exist', $interface));
        }

        if (!in_array($interface, $this->interfaces)) {
            $this->interfaces[] = $interface;
        }

        return $this;
    }

    private function getPropertyMocker(): ?PropertiesMocker
    {
        if (!isset($this->extends)) {
            return null;
        }

        if (str_contains($this->extends, '@anonymous')) {
            return new PropertiesMocker($this->extends);
        }

        $toMock = eval('return new class extends \\' . $this->extends . ' {'
            . 'public function __construct() { }'
            . '};'
        );

        return new PropertiesMocker($toMock::class);
    }
}
