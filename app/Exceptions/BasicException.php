<?php

namespace App\Exceptions;

use App\Traits\Responsable;
use Illuminate\Http\JsonResponse;

abstract class BasicException extends \Exception
{
    use Responsable;

    /**
     * Constructor.
     *
     * @param string $message
     * @param int $code
     * @param null $previous
     * @param mixed $moreData
     */
    public function __construct(string $message = "", int $code = 0, $previous = null, private readonly mixed $moreData = [])
    {
        if ($message === "") {
            $message = $this->getDefaultMessage();
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Render basic exception.
     *
     * @param $request
     *
     * @return JsonResponse
     */
    public function render($request): JsonResponse
    {
        $message = json_decode($this->getMessage(), true) ?? $this->getMessage();

        $data = ['code' => $this->getHttpCode(), 'message' => $message];

        if ($this->moreData !== []) {
            $data['info'] = $this->moreData;
        }

        return $this->error($data, $this->getHttpCode());
    }

    /**
     * Get default message.
     *
     * @return string
     */
    abstract protected function getDefaultMessage(): string;

    /**
     * Get http code.
     *
     * @return int
     */
    abstract protected function getHttpCode(): int;
}
