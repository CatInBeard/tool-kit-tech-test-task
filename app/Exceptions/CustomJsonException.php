<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class CustomJsonException extends Exception
{
    protected array $data;
    protected int $statusCode;

    public function __construct(array $data, int $statusCode = 500)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;

        parent::__construct('JSON Exception', $statusCode);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function render(): JsonResponse
    {
        return new JsonResponse($this->data, $this->statusCode);
    }
}
