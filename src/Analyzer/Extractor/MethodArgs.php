<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer\Extractor;

use Exan\Moock\Analyzer\TokenEmitter;
use Exan\Moock\Analyzer\TokenFilter;

class MethodArgs
{
    private static function anonymousClassInternalName(int $line, int $i): string
    {
        return $line . '$' . $i;
    }

    private int $anonymousClassIndex = 0;

    /**
     *
     * @param TokenEmitter $tokenEmitter
     * @param array{0: string, 1: string, 2: string} $toCapture
     * @param array &$captured
     * @return void
     */
    public function __construct(
        private readonly TokenEmitter $tokenEmitter,
        private readonly array $toCapture,
        public ?array &$captured,
    ) {
        // Classes are not allowed to start with a number. This indicates an anonymous class is targeted.
        if (is_numeric($this->toCapture[0][0])) {
            $this->targetAnonymousClasses();
        } else {
            $this->targetRealClasses();
        }
    }

    private function targetRealClasses(): void
    {
        $this->tokenEmitter->on(
            TokenFilter::ofType(T_CLASS),
            fn () => $this->tokenEmitter->once(
                TokenFilter::ofType(T_STRING),
                $this->handleClass(...)
            ),
        );
    }

    private function targetAnonymousClasses(): void
    {
        $this->tokenEmitter->onLineChange(function () {
            $this->anonymousClassIndex = 0;
        });

        $this->tokenEmitter->on(
            TokenFilter::ofType(T_NEW),
            fn () => $this->tokenEmitter->once(
                fn (string|array $token) => is_string($token) || $token[0] !== T_WHITESPACE,
                $this->handlePotentialAnonymousClass(...),
            )
        );
    }

    /**
     * @param 'class'|'method'|'arg' $type
     * @param string $value
     * @return bool
     */
    private function shouldCapture(string $type, string $value): bool
    {
        $position = [
            'class' => 0,
            'method' => 1,
            'arg' => 2,
        ][$type];

        if ($this->toCapture[$position] === $value) {
            return true;
        }

        return false;
    }

    private function handleClass(array $token)
    {
        if (!$this->shouldCapture('class', $token[1])) {
            return;
        }

        $blockLevel = 0;

        $this->tokenEmitter->counter(TokenFilter::eq('{'), TokenFilter::eq('}'), $blockLevel);

        $capture = $this->tokenEmitter->on(
            TokenFilter::ofType(T_FUNCTION),
            fn () => $this->tokenEmitter->once(
                TokenFilter::ofType(T_STRING),
                $this->handleMethod(...),
            )
        );

        $this->tokenEmitter->once(function (string|array $token) use (&$blockLevel) {
            return $blockLevel === 0 && $token === '}';
        }, function () use ($capture) {
            $this->tokenEmitter->remove($capture);
        });
    }

    public function handlePotentialAnonymousClass(string|array $token)
    {
        if (!is_array($token) || $token[0] !== T_CLASS) {
            return;
        }

        $fakeToken = [
            T_STRING,
            self::anonymousClassInternalName($token[2], $this->anonymousClassIndex++),
            $token[2],
        ];

        $this->handleClass($fakeToken);
    }

    public function handleMethod(array $token)
    {
        if (!$this->shouldCapture('method', $token[1])) {
            return;
        }

        $capture = $this->tokenEmitter->on(TokenFilter::ofType(T_VARIABLE), $this->handleArg(...));

        $this->tokenEmitter->once(
            TokenFilter::any(
                TokenFilter::eq('{'),
                TokenFilter::eq(';'),
            ),
            fn () => $this->tokenEmitter->remove($capture),
        );
    }

    public function handleArg(array $token)
    {
        if (!$this->shouldCapture('arg', $token[1])) {
            return;
        }

        $arg = [];
        $parenthesisLevel = 0;

        $toCancel = [];

        $toCancel[] = $this->tokenEmitter->on(TokenFilter::eq('('), function () use (&$parenthesisLevel) {
            $parenthesisLevel++;
        });

        $toCancel[] = $this->tokenEmitter->on(TokenFilter::eq(')'), function () use (&$parenthesisLevel) {
            $parenthesisLevel--;
        });

        $toCancel[] = $this->tokenEmitter->all(function (array|string $token) use (&$arg) {
            $arg[] = $token;
        });

        $this->tokenEmitter->once(function (string|array $token) use (&$parenthesisLevel) {
            $nextArgStarting = $parenthesisLevel === 0 && $token === ',';
            $methodEnding = $parenthesisLevel === -1 && $token === ')';

            return $nextArgStarting || $methodEnding;
        }, function () use (&$arg, $toCancel) {
            $this->tokenEmitter->remove(...$toCancel);

            $this->captured = array_slice($arg, 3, -1);
        });
    }
}
