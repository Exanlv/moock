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
    public static function after(DateTimeInterface $beforeDate): Closure
    {
        return fn (DateTimeInterface $date): bool => $date > $beforeDate;
    }
}
