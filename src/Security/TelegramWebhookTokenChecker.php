<?php

namespace App\Security;

use App\Exception\AccessDeniedException;

class TelegramWebhookTokenChecker
{

    /**
     * TelegramWebhookTokenChecker constructor.
     */
    public function __construct(protected string $webhookToken)
    {
    }

    public function checkToken(string $webhookToken) {
        if ($webhookToken !== $this->webhookToken) {
            throw new AccessDeniedException();
        }
    }
}
