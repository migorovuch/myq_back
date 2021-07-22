<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedBookingException extends ApiException
{
    const DEFAULT_MSG = 'This booking is only available for authorized users. Please, sign in.';

    const DEFAULT_CODE = Response::HTTP_FORBIDDEN;

}
