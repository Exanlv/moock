<?php

declare(strict_types=1);

namespace Tests\Components;

use DateTimeInterface;

interface UserServiceInterface
{
    public function userExists(string $email): bool;
    public function createUser(string $email, string $username, string $password): void;
    public function getUsersCreatedBefore(DateTimeInterface $dateTime): array;
    public function getUsersByAge(int $age): array;
    public function deleteUsersByEmail(array $emails): void;
}
