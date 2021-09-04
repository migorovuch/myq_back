<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class ClientHasNoUserRelationException extends ApiException
{
    const DEFAULT_MSG = 'Client has no user relation';
}
