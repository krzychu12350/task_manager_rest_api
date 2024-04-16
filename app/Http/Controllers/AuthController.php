<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\RegisterResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller handling authentication operations.
 */
class AuthController extends Controller
{
    /**
     * The authentication service.
     *
     * @var AuthService
     */
    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request The login request.
     *
     * @return JsonResponse The JSON response.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $authenticatedUser = $this->authService->login($request->validated());

        return $this->success(
            new LoginResource([
                'token' => $this->authService->createAccessToken($authenticatedUser)
            ])
        );
    }

    /**
     * Handle user registration.
     *
     * @param RegisterRequest $request The registration request.
     *
     * @return JsonResponse The JSON response.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $registeredUser = $this->authService->registerNewUser($request->validated());

        return $this->success(
            new RegisterResource($registeredUser),
            'User registered successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Handle user logout.
     *
     * @return JsonResponse The JSON response.
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout(AuthHelper::getCurrentUser());

        return $this->success([], 'User logged out successfully');
    }
}
