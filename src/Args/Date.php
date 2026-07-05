<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;
use DateTimeInterface;

class Date
{
    /**
     * @psalm-return Closure(DateTimeInterface):bool
     */
    public static function before(DateTimeInterface $beforeDate): Closure
    {
        return fn (DateTimeInterface $date): bool => $date < $beforeDate;
    }

    /**
     * @psalm-return Closure(DateTimeInterface):bool
     */
    public static function after(DateTimeInterface $afterDate): Closure
    {
        return fn (DateTimeInterface $date): bool => $date > $afterDate;
    }

    /**
     * @psalm-return Closure(DateTimeInterface):bool
     */
    public static function between(DateTimeInterface $start, DateTimeInterface $end): Closure
    {
        return fn (DateTimeInterface $date): bool => $date >= $start && $date <= $end;
    }

    /**
     * @psalm-return Closure(DateTimeInterface):bool
     */
    public static function equal(DateTimeInterface $target): Closure
    {
        return fn (DateTimeInterface $date): bool => $date == $target;
    }

    /**
     * @psalm-return Closure(DateTimeInterface):bool
     */
    public static function sameDay(DateTimeInterface $target): Closure
    {
        return fn (DateTimeInterface $date): bool => $date->format('Y-m-d') === $target->format('Y-m-d');
    }

    /**
     * @psalm-return Closure(DateTimeInterface):bool
     */
    public static function inPast(): Closure
    {
        return fn (DateTimeInterface $date): bool => $date < new \DateTimeImmutable('now');
    }

    /**
     * @psalm-return Closure(DateTimeInterface):bool
     */
    public static function inFuture(): Closure
    {
        return fn (DateTimeInterface $date): bool => $date > new \DateTimeImmutable('now');
    }
}
