<?php

declare(strict_types=1);

namespace Tests\Components;

interface ProductServiceInterface
{
    public function productExists(int $productId): ?array;
    public function purchase(int $productId, string $userEmail): bool;
}
