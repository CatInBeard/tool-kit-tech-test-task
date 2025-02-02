<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class ErrorJsonException extends CustomJsonException
{
    public function __construct(string $error, $statusCode = 500)
    {

        parent::__construct(
            compact('error'),
            $statusCode
        );
    }
}
