<?php

namespace tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\TestCase;

/**
 * Class UserRepositoryTest
 */
class UserRepositoryTest extends TestCase
{
    protected UserRepository $userRepository;
    protected array $userData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = new UserRepository();

        $this->userData = [
            'name' => $this->generateRandomUsername(),
            'email' => $this->generateRandomEmail(),
            'password' => bcrypt($this->generateRandomStrongPassword()),
        ];
    }

    /**
     * Test that it can create a user.
     *
     * @return void
     */
    public function test_it_can_create_a_user()
    {
        $user = $this->userRepository->create($this->userData);

        $this->assertInstanceOf(User::class, $user);

        $this->assertEquals($this->userData['name'], $user->name);
        $this->assertEquals($this->userData['email'], $user->email);
    }

    /**
     * Test that it can find a user by email.
     *
     * @return void
     */
    public function test_it_can_find_user_by_email()
    {
        $this->userRepository->create($this->userData);

        $foundUser = $this->userRepository->findByEmail($this->userData['email']);

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($this->userData['name'], $foundUser->name);
        $this->assertEquals($this->userData['email'], $foundUser->email);
    }

    /**
     * Test that it returns null when a user is not found by email.
     *
     * @return void
     */
    public function test_it_returns_null_when_user_not_found_by_email()
    {
        $nonExistentEmail = 'nonexistent@example.com';
        $foundUser = $this->userRepository->findByEmail($nonExistentEmail);

        $this->assertNull($foundUser);
    }
}
