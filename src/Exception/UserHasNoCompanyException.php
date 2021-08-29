<?php

namespace App\Exception;

class UserHasNoCompanyException extends ApiException
{
    const DEFAULT_MSG = 'User has no company created';
}
