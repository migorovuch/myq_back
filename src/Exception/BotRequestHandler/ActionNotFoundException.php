<?php

namespace App\Exception\BotRequestHandler;

use App\Exception\ApiException;

class ActionNotFoundException extends ApiException
{
    const DEFAULT_MSG = 'Action not found';

    const DEFAULT_CODE = 400;
}
