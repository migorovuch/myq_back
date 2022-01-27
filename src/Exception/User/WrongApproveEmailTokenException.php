<?php

namespace App\Exception\User;

use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;

class WrongApproveEmailTokenException extends ApiException
{
    const DEFAULT_MSG = 'Wrong approve email token';

    const DEFAULT_CODE = Response::HTTP_METHOD_NOT_ALLOWED;
}