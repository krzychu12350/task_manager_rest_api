<?php

namespace App\Helpers;

use App\Models\User;

class AuthHelper
{
    /**
     * Get the current authenticated user.
     *
     * @return User|null The authenticated user, or null if no user is authenticated.
     */
    public static function getCurrentUser(): ?User
    {
        return request()->user();
    }
}
