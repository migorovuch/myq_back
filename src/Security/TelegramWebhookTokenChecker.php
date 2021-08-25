<?php

namespace App\Security;

use App\Exception\AccessDeniedException;
use Psr\Log\LoggerInterface;

class TelegramWebhookTokenChecker
{

    /**
     * TelegramWebhookTokenChecker constructor.
     */
    public function __construct(protected LoggerInterface $logger, protected string $webhookToken)
    {
    }

    public function checkToken(string $webhookToken)
    {
        if ($webhookToken !== $this->webhookToken) {
            $this->logger->error('Incorrect webhook token', ['w1' => $webhookToken, 'w2' => $this->webhookToken]);
            throw new AccessDeniedException();
        }
    }
}
