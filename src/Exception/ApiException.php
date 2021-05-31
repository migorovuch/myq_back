<?php

namespace App\Exception;

use LogicException;
use Throwable;

class ApiException extends LogicException implements ApiExceptionInterface
{
    const DEFAULT_MSG = '';

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ? $message : static::DEFAULT_MSG, $code, $previous);
    }
}
