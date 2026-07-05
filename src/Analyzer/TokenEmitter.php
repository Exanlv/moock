<?php

declare(strict_types=1);

namespace Exan\Moock\Analyzer;

use Closure;

class TokenEmitter
{
    /**
     * @var array<int, array{match: Closure, handler: Closure, once: bool}>
     */
    private array $handlers = [];

    public function on(Closure $match, Closure $handler): int
    {
        $id = $this->getId();
        $this->handlers[$id] = ['match' => $match, 'handler' => $handler];

        return $id;
    }

    public function onLineChange(Closure $handler): int
    {
        $line = 0;

        return $this->all(function (string|array $token) use (&$line, $handler) {
            if (is_string($token)) {
                return;
            }

            if ($token[2] > $line) {
                $line = $token[2];
                $handler();
            }
        });
    }

    public function all(Closure $handler): int
    {
        $id = $this->getId();
        $this->handlers[$id] = ['handler' => $handler];

        return $id;
    }

    public function once(Closure $match, Closure $handler): void
    {
        $id = $this->getId();
        $this->handlers[$id] = ['match' => $match, 'handler' => $handler, 'once' => true];
    }

    public function counter(Closure $up, Closure $down, int &$value): array
    {
        return [
            $this->on($up, function () use (&$value) {
                $value++;
            }),
            $this->on($down, function () use (&$value) {
                $value--;
            })
        ];
    }

    public function remove(int ...$ids): void
    {
        foreach ($ids as $id) {
            unset($this->handlers[$id]);
        }
    }

    private function getId(): int
    {
        do {
            $id = rand(100_000, 999_999);
        } while (isset($this->handlers[$id]));

        return $id;
    }

    public function emit(array $tokens): void
    {
        $wasWhitespace = false;
        foreach ($tokens as $token) {
            if (static::isType($token, T_COMMENT)) {
                continue;
            }

            $isWhitespace = static::isType($token, T_WHITESPACE);
            if ($isWhitespace) {
                if ($wasWhitespace) {
                    continue;
                }

                $wasWhitespace = true;
            } else {
                $wasWhitespace = false;
            }

            $this->emitSingularToken($token);
        }
    }

    private function emitSingularToken(array|string $token): void
    {
        foreach ($this->handlers as $i => $handler) {
            if (!isset($handler['match']) || $handler['match']($token)) {
                $handler['handler']($token);

                if ($handler['once'] ?? false) {
                    unset($this->handlers[$i]);
                }
            }
        }

    }

    private static function isType(string|array $token, int $type): bool
    {
        return is_array($token) && $token[0] === $type;
    }
}
