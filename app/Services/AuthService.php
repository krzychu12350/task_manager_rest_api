<?php

namespace App\Services;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

/**
 * Service class for handling authentication-related operations.
 */
class AuthService extends BaseService
{
    /**
     * User repository instance.
     *
     * @var UserRepositoryInterface
     */
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    /**
     * Authenticate a user with the given credentials.
     *
     * @param array $credentials The user credentials.
     *
     * @return Authenticatable or throw InvalidCredentialsException exception.
     */
    public function login(array $credentials): Authenticatable
    {
        if (Auth::attempt($credentials)) {
            return Auth::user();
        }

        $this->throw(new InvalidCredentialsException());
    }

    /**
     * Register a new user.
     *
     * @param array $data The user data for registration.
     *
     * @return User The newly registered user.
     */
    public function registerNewUser(array $data): User
    {
        return $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Log out the given user.
     *
     * @param User $user The user to log out.
     *
     * @return void
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Create an access token for the given user.
     *
     * @param Authenticatable $user The user for whom to create the token.
     *
     * @return string The generated access token.
     */
    public function createAccessToken(Authenticatable $user): string
    {
        return $user->createToken('Personal Access Token')->plainTextToken;
    }
}
