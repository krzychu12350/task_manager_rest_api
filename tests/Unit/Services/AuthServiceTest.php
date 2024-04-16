<?php

namespace tests\Unit\Services;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Requests\Auth\LoginRequest;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Mockery;
use Symfony\Component\HttpFoundation\Request;
use Tests\TestCase;

/**
 * Class AuthServiceTest
 */
class AuthServiceTest extends TestCase
{
    protected AuthService $authService;
    protected UserRepositoryInterface $userRepository;

    private array $credentials;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->authService = Mockery::mock(new AuthService($this->userRepository));

        $this->credentials = [
            'email' => $this->generateRandomEmail(),
            'password' => $this->generateRandomStrongPassword(),
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * Test that it can login with valid credentials.
     *
     * @return void
     */
    public function test_it_can_login_with_valid_credentials()
    {
        $user = User::factory()->create($this->credentials);

        Auth::shouldReceive('attempt')->once()->with($this->credentials)->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        $result = $this->authService->login($this->credentials);

        $this->assertInstanceOf(Authenticatable::class, $result);
        $this->assertEquals($user, $result);
    }

    /**
     * Test that it can register a new user.
     *
     * @return void
     */
    public function test_it_can_register_new_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $this->userRepository->shouldReceive('create')->once()->andReturn(new User($userData));

        $result = $this->authService->registerNewUser($userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userData['name'], $result->name);
        $this->assertEquals($userData['email'], $result->email);
    }

    /**
     * Test that it can logout a user.
     *
     * @return void
     */
    public function test_it_can_logout_user()
    {
        $tokenRelationMock = Mockery::mock();
        $tokenRelationMock->shouldReceive('delete')->once();
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('tokens')->once()->andReturn($tokenRelationMock);

        $this->authService->logout($userMock);
    }

    /**
     * Test that it can create an access token.
     *
     * @return void
     */
    public function test_it_can_create_access_token()
    {
        $user = User::factory()->create();

        $token = $this->authService->createAccessToken($user);

        $this->assertNotEmpty($token);

        $accessToken = PersonalAccessToken::findToken($token);

        $this->assertNotNull($accessToken);
        $this->assertEquals($user->id, $accessToken->tokenable_id);
    }

    /**
     * Test that login method throws exception for invalid credentials.
     *
     * @return void
     */
    public function test_login_method_throws_exception_for_invalid_credentials()
    {
        Auth::shouldReceive('attempt')->andReturn(false);

        $invalidCredentials = [
            'email' => 'invalid@gmail.com',
            'password' => 'invalid_password'
        ];

        $this->expectException(InvalidCredentialsException::class);

        $this->authService->login($invalidCredentials);
    }
}
