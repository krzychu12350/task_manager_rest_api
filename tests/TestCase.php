<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

    /**
     * Act as an authenticated user.
     *
     * @return User
     */
    protected function actAsAuthenticatedUser(): User
    {
        $user = User::factory()->createOne();
        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Generate a random username.
     *
     * @return string
     */
    protected function generateRandomUsername(): string
    {
        return 'user_' . Str::random(8);
    }

    /**
     * Generate a random email address.
     *
     * @return string
     */
    protected function generateRandomEmail(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $randomString = '';

        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString . '@example.com';
    }

    /**
     * Generate a random strong password.
     *
     * @return string
     */
    protected function generateRandomStrongPassword(): string
    {
        return $this->faker->password(16, 32);
    }

}
