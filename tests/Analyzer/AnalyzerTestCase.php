<?php

declare(strict_types=1);

namespace Tests\Analyzer;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AnalyzerTestCase extends TestCase
{
    protected function assertContainsTokenAnywhere(string $needle, array $haystack, string $message = ''): void
    {
        $found = $this->containsStringRecursive($needle, $haystack);

        Assert::assertTrue(
            $found,
            $message !== '' ? $message : "Failed asserting that array contains token '{$needle}'."
        );
    }

    protected function assertDoesntContainTokenAnywhere(string $needle, array $haystack, string $message = ''): void
    {
        $found = $this->containsStringRecursive($needle, $haystack);

        Assert::assertFalse(
            $found,
            $message !== '' ? $message : "Failed asserting that array does not contain token '{$needle}'."
        );
    }

    private function containsStringRecursive(string $needle, mixed $value): bool
    {
        if (is_string($value)) {
            return $value === $needle;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if ($this->containsStringRecursive($needle, $item)) {
                    return true;
                }
            }
        }

        return false;
    }
}
