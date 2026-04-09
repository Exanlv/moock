<?php

declare(strict_types=1);

namespace Tests\Components;

use DateTimeInterface;

class UserService implements UserServiceInterface
{
    public array $users = [];

    public function isValidEmail(string $email): bool
    {
        throw new \Exception('Not implemented');
    }

    public function userExists(string $email): bool
    {
        return in_array($email, $this->users);
    }

    public function createUser(string $email, string $username, string $password): void
    {
        throw new \Exception('Not implemented');
    }

    public function getUsersCreatedBefore(DateTimeInterface $dateTime): array
    {
        throw new \Exception('Not implemented');
    }

    public function getUsersByAge(int $age): array
    {
        throw new \Exception('Not implemented');
    }

    public function deleteUsersByEmail(array $emails): void
    {
        throw new \Exception('Not implemented');
    }
}
