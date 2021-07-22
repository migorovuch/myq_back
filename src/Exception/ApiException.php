<?php

namespace App\Exception;

use LogicException;
use Throwable;

class ApiException extends LogicException implements ApiExceptionInterface
{
    const DEFAULT_MSG = '';

    const DEFAULT_CODE = 0;

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?: static::DEFAULT_MSG, $code ?: static::DEFAULT_CODE, $previous);
    }
}
