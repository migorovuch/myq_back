<?php

namespace App\Exception;

use Throwable;

class UserHasNoCompanyException extends ApiException
{
    const DEFAULT_MSG = 'User has no company created';
}
