<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class TryingToUseExistingAccountException extends ApiException
{
    const DEFAULT_MSG = 'You already have account. Please, Sign in.';

    const DEFAULT_CODE = Response::HTTP_FORBIDDEN;
}
