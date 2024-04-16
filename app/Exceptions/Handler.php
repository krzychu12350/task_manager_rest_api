<?php

namespace App\Exceptions;

use App\Exceptions\General\MethodNotAllowedException;
use App\Exceptions\General\NotFoundException;
use App\Exceptions\General\UnauthenticatedException;
use App\Exceptions\General\UnauthorizedActionException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * General exceptions map.
     *
     * @var array|string[]
     */
    private array $exceptionsMap = [
        AuthenticationException::class => UnauthenticatedException::class,
        AuthorizationException::class => UnauthorizedActionException::class,
        ModelNotFoundException::class => NotFoundException::class,
        NotFoundHttpException::class => NotFoundException::class,
        RouteNotFoundException::class =>  UnauthenticatedException::class,
        MethodNotAllowedHttpException::class => MethodNotAllowedException::class
    ];

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        foreach ($this->exceptionsMap as $from => $to) {
            $this->map($from, $to);
        }
    }
}
