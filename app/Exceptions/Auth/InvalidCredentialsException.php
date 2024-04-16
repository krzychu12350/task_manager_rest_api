<?php

namespace App\Exceptions\Auth;

use App\Exceptions\BasicException;
use Illuminate\Http\Response;

class InvalidCredentialsException extends BasicException
{
    /**
     * Return http code.
     *
     * @return int
     */
    protected function getHttpCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }

    /**
     * Get default message.
     *
     * @return string
     */
    protected function getDefaultMessage(): string
    {
        return "Invalid credentials";
    }
}
