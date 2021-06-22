<?php

namespace App\Exception;

class EntryNotFoundException extends ApiException
{
    const DEFAULT_MSG = 'Entry not found';
}
