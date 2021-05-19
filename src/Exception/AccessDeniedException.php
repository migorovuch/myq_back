<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccessDeniedException as BaseAccessDeniedException;

class AccessDeniedException extends BaseAccessDeniedException implements ApiExceptionInterface
{
}
