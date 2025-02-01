<?php

namespace App\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Exception;

class CustomJsonException extends Exception
{
    protected $data;
    protected $statusCode;

    public function __construct($data, $statusCode = 500)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;

        parent::__construct('JSON Exception', $statusCode);
    }

    public function getData()
    {
        return $this->data;
    }

    public function render()
    {
        return new JsonResponse($this->data, $this->statusCode);
    }

}
