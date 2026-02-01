<?php

declare(strict_types=1);

namespace Exan\Moock;

use Closure;
use RuntimeException;

trait FiltersMethodArgs
{
    private function filterArgs(array $inputs, array $filters): array
    {
        $filters = array_is_list($filters)
            ? $this->convertArgsToDictionary($inputs, $filters)
            : $filters;

        foreach ($filters as $name => $valueOrValidator) {
            $validator = $valueOrValidator instanceof Closure
                ? fn ($call): bool => $valueOrValidator($call[$name])
                : fn ($call): bool => $call[$name] === $valueOrValidator;

            $inputs = array_filter($inputs, $validator);
        }

        return $inputs;
    }

    private function convertArgsToDictionary(array $inputs, array $filters)
    {
        $argKeys = array_keys($inputs[0]);

        if (count($filters) > count($argKeys)) {
            throw new RuntimeException(sprintf(
                'Method only has %d parameters, %d expectations given. Note: variadic args are validated as a singular array',
                count($argKeys),
                count($filters),
            ));
        }

        while (count($filters) < count($argKeys)) {
            array_pop($argKeys);
        }

        return array_combine($argKeys, $filters);
    }
}
