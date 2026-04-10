<?php

declare(strict_types=1);

namespace Exan\Moock\Expector;

use Closure;
use Exan\Moock\Dto\DetailedMethodCall;
use Exan\Moock\Dto\MethodCall;
use Exan\Moock\MockedClassInterface;
use Exan\Moock\MoockAssert;
use ReflectionFunction;

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
        $ref = new ReflectionFunction($closure);
        $expectation = new Expectation($ref);

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

        $actual = $this->getActualCalls();

        $matches = array_map(fn (DetailedMethodCall $call) => array_map(
            fn (Expectation $expectation) => $expectation->matches($call),
            $this->expectations
        ), $actual);

        $this->assertCallsInOrder($matches);
    }

    /**
     * @param Array<int, bool[]> $matches
     */
    private function assertCallsInOrder($matches): void
    {
        $expectationsCount = count($this->expectations);
        $i = 0;

        while (count($matches) > 0) {
            $toCheck = array_shift($matches);
            if ($toCheck[$i] === true) {
                $i++;

                if ($i === $expectationsCount) {
                    $this->succeed();

                    return;
                }
            }
        }

        $this->fail($i);
    }

    private function succeed(): void
    {
        // Testing tools may complain if no assertion is made
        MoockAssert::assert(true, true, '');
    }

    private function fail(int $i): void
    {
        $message = $i === 0
            ? sprintf('Failed asserting %s was called', $this->getFunctionNameAt($i))
            : sprintf(
                'Failed asserting %s was called after %s',
                $this->getFunctionNameAt($i),
                $this->getFunctionNameAt($i - 1),
            );


        MoockAssert::assert(false, true, sprintf($message));
    }

    private function getFunctionNameAt(int $i): string
    {
        $expectation = $this->expectations[$i];
        return $expectation->ref->getName();
    }


    /**
     * @return DetailedMethodCall[]
     */
    private function getActualCalls(): array
    {
        /** @var MockedClassInterface[] */
        $objects = array_map(fn (Expectation $expectation) => $expectation->ref->getClosureThis(), $this->expectations);

        $objectHashes = [];
        $totalCalls = [];

        foreach ($objects as $object) {
            $hash = spl_object_hash($object);
            if (in_array($hash, $objectHashes)) {
                continue;
            }

            $objectHashes[] = $hash;

            $calls = $object->__getAllCalls();

            foreach ($calls as $methodName => $methodCalls) {
                $totalCalls = [
                    ...$totalCalls,
                    ...array_map(fn (MethodCall $methodCall) => new DetailedMethodCall(
                        $hash,
                        $methodName,
                        $methodCall
                    ), $methodCalls),
                ];
            }
        }

        usort(
            $totalCalls,
            fn (DetailedMethodCall $a, DetailedMethodCall $b) => $a->call->methodCallId > $b->call->methodCallId ? 1 : -1
        );

        return $totalCalls;
    }
}
