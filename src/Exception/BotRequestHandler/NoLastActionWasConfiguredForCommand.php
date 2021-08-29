<?php

namespace App\Exception\BotRequestHandler;

use App\Exception\ApiException;

class NoLastActionWasConfiguredForCommand extends ApiException
{
    const DEFAULT_MSG = 'No last action was configured for action';

    const DEFAULT_CODE = 400;
}
