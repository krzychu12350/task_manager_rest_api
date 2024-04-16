<?php

namespace App\Exceptions\General;

use App\Exceptions\BasicException;
use Illuminate\Http\Response;

class NotFoundException extends BasicException
{
    /**
     * Return http code.
     *
     * @return int
     */
    protected function getHttpCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    /**
     * Get default message.
     *
     * @return string
     */
    protected function getDefaultMessage(): string
    {
        return "Resource not found";
    }
}