<?php

namespace App\Exceptions\General;

use App\Exceptions\BasicException;
use Illuminate\Http\Response;

class UnauthorizedActionException extends BasicException
{
    /**
     * Return http code.
     *
     * @return int
     */
    protected function getHttpCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }

    /**
     * Get default message.
     *
     * @return string
     */
    protected function getDefaultMessage(): string
    {
        return "Unauthorized action";
    }
}