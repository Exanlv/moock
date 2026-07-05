<?php

declare(strict_types=1);

namespace Exan\Moock;

use Closure;
use RuntimeException;

trait FiltersMethodArgs
{
    /**
     * @psalm-return array<int<0, max>, mixed>
     */
    private function filterArgs(array $inputs, array $filters): array
    {
        if (empty($inputs)) {
            return [];
        }

        $inputs = array_values($inputs);
        $keys = array_keys($inputs[0]);

        $filters = array_is_list($filters)
            ? $this->convertArgsToDictionary($inputs, $filters)
            : $filters;

        foreach ($filters as $name => $valueOrValidator) {
            if (is_int($name)) {
                $name = $keys[$name];
            }

            $validator = $valueOrValidator instanceof Closure
                ? fn ($call): bool => $valueOrValidator($call[$name])
                : fn ($call): bool => $call[$name] === $valueOrValidator;

            $inputs = array_filter($inputs, $validator);
        }

        return $inputs;
    }

    /**
     * @template TArray of array<array-key, mixed>
     *
     * @param non-empty-list<TArray> $inputs
     * @param list<mixed> $filters
     *
     * @return array<key-of<TArray>, mixed>
     */
    private function convertArgsToDictionary(array $inputs, array $filters): array
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
