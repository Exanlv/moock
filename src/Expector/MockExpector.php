<?php

declare(strict_types=1);

namespace Exan\Moock\Expector;

use Closure;

/** @internal */
class MockExpector
{
    private static int $methodCallId = 0;

    /** @var Expectation[] */
    private array $expectations = [];

    public static function getMethodCallId(): int
    {
        return ++static::$methodCallId;
    }

    public function expect(Closure $closure): Expectation
    {
        $expectation = new Expectation($closure);

        // @todo validate mocked class

        $this->expectations[] = $expectation;

        return $expectation;
    }

    /**
     * @template T
     * @param Closure(Closure $expect): void $expectation
     */
    public function validate(Closure $expectation): void
    {
        $expectation($this->expect(...));

        // dd($this->expectations);
    }
}
