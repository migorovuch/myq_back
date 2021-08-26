<?php

namespace App\Security;

use App\Exception\AccessDeniedException;
use Psr\Log\LoggerInterface;

class TelegramWebhookTokenChecker
{

    /**
     * TelegramWebhookTokenChecker constructor.
     */
    public function __construct(protected LoggerInterface $appLogger, protected string $webhookToken)
    {
    }

    public function checkToken(string $webhookToken)
    {
        if ($webhookToken !== $this->webhookToken) {
            throw new AccessDeniedException();
        }
    }
}
