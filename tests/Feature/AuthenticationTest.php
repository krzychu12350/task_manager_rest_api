<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    protected string $strongPassword;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->strongPassword = $this->generateRandomStrongPassword();
    }

    /**
     * Test that the API successfully logs in a user.
     *
     * @return void
     */
    public function test_api_successful_login_user(): void
    {
        $randomEmail = $this->generateRandomEmail();

        User::factory()->create([
            'email' => $randomEmail,
            'password' => bcrypt($this->strongPassword),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $randomEmail,
            'password' => $this->strongPassword,
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token'
                ],
                'message'
            ]);
    }

    /**
     * Test that the API returns invalid login credentials response.
     *
     * @return void
     */
    public function test_api_returns_invalid_login_credentials_response(): void
    {
        $user = User::factory()->create([
            'email' => $this->generateRandomEmail(),
            'password' => bcrypt($this->strongPassword),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $user->password . "1",
        ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Invalid credentials'
                ],
            ]);
    }

    /**
     * Test that the API successfully registers a user.
     *
     * @return void
     */
    public function test_api_successful_register_user(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => $this->generateRandomUsername(),
            'email' => $this->generateRandomEmail(),
            'password' => $this->strongPassword,
            'password_confirmation' => $this->strongPassword,
        ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user'
                ],
                'message'
            ]);
    }

    /**
     * Test that the API returns errors during registration with existing email.
     *
     * @return void
     */
    public function test_api_returns_errors_during_registration_with_existing_email(): void
    {
        $existingUser = User::factory()->create();

        $this->postJson('/api/auth/register', [
            'name' => $this->generateRandomUsername(),
            'email' => $existingUser->email,
            'password' => 'StrongPassword12?3',
            'password_confirmation' => 'StrongPassword12?3',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The email has already been taken.',
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ]
                ]
            ]);
    }

    /**
     * Test that the API successfully logs out a user.
     *
     * @return void
     */
    public function test_api_successful_logout_user(): void
    {
        $user = User::factory()->create(
            [
                'name' => $this->generateRandomUsername(),
                'email' => $this->generateRandomEmail(),
                'password' => $this->strongPassword,
            ]
        );

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $this->strongPassword,
        ]);

        $token = $loginResponse->json('data.token');

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/auth/logout')
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'data' => [],
                'message' => 'User logged out successfully',
            ]);
    }

    /**
     * Test that the API returns unauthorized status code during logout without authentication token.
     *
     * @return void
     */
    public function test_api_return_unauthorized_status_code_during_logout_without_authentication_token(): void
    {
        $this->postJson('/api/auth/logout')
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Unauthenticated'
                ]
            ]);
    }

    /**
     * Test that the API returns validation errors during login with missing required fields.
     *
     * @return void
     */
    public function test_api_returns_validations_error_during_login_with_missing_required_fields(): void
    {
        $this->postJson('/api/auth/login', [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'email',
                'password',
            ]);
    }

    /**
     * Test that the API returns validation errors during registration with missing required fields.
     *
     * @return void
     */
    public function test_api_returns_validation_errors_during_registration_with_missing_required_fields(): void
    {
        $this->postJson('/api/auth/register', [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
            ]);
    }

    /**
     * Test that the API returns validation errors during registration with invalid email format.
     *
     * @return void
     */
    public function test_api_returns_validation_errors_during_registration_with_invalid_email_format(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => $this->generateRandomUsername(),
            'email' => $this->generateRandomEmail() . '$',
            'password' => $this->strongPassword,
            'password_confirmation' => $this->strongPassword,
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    /**
     * Test that the API returns validation errors during registration with weak password.
     *
     * @return void
     */
    public function test_api_returns_validation_errors_during_registration_with_weak_password(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => $this->generateRandomUsername(),
            'email' => $this->generateRandomEmail(),
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    /**
     * Test that the API returns validation errors during registration with unmatched password confirmation.
     *
     * @return void
     */
    public function test_api_returns_validation_errors_during_registration_with_not_matched_password_confirmation(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => $this->generateRandomUsername(),
            'email' => $this->generateRandomEmail(),
            'password' => $this->strongPassword,
            'password_confirmation' => $this->strongPassword . '#',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'password',
            ]);
    }

    /**
     * Test that the API returns errors during logout with an invalid authentication token.
     *
     * @return void
     */
    public function test_api_returns_errors_during_logout_with_invalid_authentication_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestInvalidToken')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token . 'f',
        ])
            ->postJson('/api/auth/logout')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test that the API returns errors during logout with an expired authentication token.
     *
     * @return void
     */
    public function test_api_returns_errors_during_logout_with_expired_authentication_token(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('Token Name')->plainTextToken;

        $accessToken = PersonalAccessToken::findToken($token);

        $expirationTime = now()->subHours(25);
        $accessToken->update([
            'expires_at' => $expirationTime,

        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
