<?php

declare(strict_types=1);

namespace Tests\Components;

interface UserServiceInterface
{
    public function userExists(string $email): bool;
    public function createUser(string $email, string $username, string $password): void;
}
