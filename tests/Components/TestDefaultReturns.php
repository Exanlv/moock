<?php

declare(strict_types=1);

namespace Tests\Components;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use Generator;
use stdClass;

class TestDefaultReturns
{
    public function returnInt(): int
    {
        return 1;
    }

    public function returnFloat(): float
    {
        return 1.0;
    }

    public function returnString(): string
    {
        return "hello";
    }

    public function returnBool(): bool
    {
        return true;
    }

    public function returnTrue(): true
    {
        return true;
    }

    public function returnFalse(): false
    {
        return false;
    }

    public function returnNull(): null
    {
        return null;
    }

    public function returnArray(): array
    {
        return [];
    }

    public function returnObject(): object
    {
        return new \stdClass();
    }

    public function returnStdClass(): stdClass
    {
        return new \stdClass();
    }

    public function returnCallable(): callable
    {
        return fn () => "called";
    }

    public function returnIterable(): iterable
    {
        return [1, 2, 3];
    }

    public function returnMixed(): mixed
    {
        return "anything";
    }

    public function returnVoid(): void
    {
        // no return
    }

    public function returnSelf(): self
    {
        return $this;
    }

    public function returnStatic(): static
    {
        return $this;
    }

    public function returnOwnClass(): TestDefaultReturns
    {
        return $this;
    }

    public function returnDateTime(): DateTime
    {
        return new DateTime();
    }

    public function returnDateTimeImmutable(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    public function returnDateInterval(): DateInterval
    {
        return new DateInterval('1 day');
    }

    public function returnDatePeriod(): DatePeriod
    {
        return DatePeriod::createFromISO8601String('R/2026-02-24T00:00:00Z/P1Y');
    }

    public function returnUserService(): UserService
    {
        return new UserService();
    }

    public function returnUserServiceInterface(): UserServiceInterface
    {
        return new UserService();
    }

    public function returnGenerator(): Generator
    {
        yield 'value';
    }
}
