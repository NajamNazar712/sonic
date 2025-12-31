<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class JsonResponseException extends Exception
{
    public JsonResponse $response;

    public function __construct(JsonResponse $response)
    {
        parent::__construct();
        $this->response = $response;
    }
}
