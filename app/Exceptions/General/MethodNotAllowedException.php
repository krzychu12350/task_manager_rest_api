<?php

namespace App\Exceptions\General;

use App\Exceptions\BasicException;
use Illuminate\Http\Response;

class MethodNotAllowedException extends BasicException
{
    /**
     * Return http code.
     *
     * @return int
     */
    protected function getHttpCode(): int
    {
        return Response::HTTP_METHOD_NOT_ALLOWED;
    }

    /**
     * Get default message.
     *
     * @return string
     */
    protected function getDefaultMessage(): string
    {
        return "Method not allowed for this route";
    }
}