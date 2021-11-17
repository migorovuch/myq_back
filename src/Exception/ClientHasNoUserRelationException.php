<?php

namespace App\Exception;

class ClientHasNoUserRelationException extends ApiException
{
    const DEFAULT_MSG = 'Client has no user relation';
}
