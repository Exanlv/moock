<?php

declare(strict_types=1);

namespace Tests\Components;

class UserService implements UserServiceInterface
{
    public function userExists(string $email): bool
    {
        throw new \Exception('Not implemented');
    }

    public function createUser(string $email, string $username, string $password): void
    {
        throw new \Exception('Not implemented');
    }
}
