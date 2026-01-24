<?php

declare(strict_types=1);

namespace Exan\Moock\Args;

use Closure;
use DateTimeInterface;

class Date
{
    public static function before(DateTimeInterface $beforeDate): Closure
    {
        return function (DateTimeInterface $date) use ($beforeDate): bool {
            return $date < $beforeDate;
        };
    }

    public static function after(DateTimeInterface $beforeDate): Closure
    {
        return function (DateTimeInterface $date) use ($beforeDate): bool {
            return $date > $beforeDate;
        };
    }
}
