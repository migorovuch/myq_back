<?php

namespace App\Exception;

use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiException extends LogicException implements ApiExceptionInterface
{
    const DEFAULT_MSG = '';

    const DEFAULT_CODE = Response::HTTP_BAD_REQUEST;

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?: static::DEFAULT_MSG, $code ?: static::DEFAULT_CODE, $previous);
    }
}
