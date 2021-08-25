<?php

namespace App\Exception\BotRequestHandler;

use App\Exception\ApiException;

class RequiredPayloadNotFoundException extends ApiException
{
    const DEFAULT_MSG = 'Required bot action payload not found';

    const DEFAULT_CODE = 400;
}
