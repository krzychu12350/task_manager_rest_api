<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

/**
 * Repository class for handling User-related database operations.
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param array $data The data to create the user with.
     *
     * @return User The created user model.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email The email address of the user to find.
     *
     * @return User|null The found user, or null if not found.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
