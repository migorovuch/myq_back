<?php

namespace App\Security;

use App\Exception\AccessDeniedException;
use Psr\Log\LoggerInterface;

class TelegramWebhookTokenChecker
{
    private string $webhookToken;

    /**
     * TelegramWebhookTokenChecker constructor.
     */
    public function __construct(protected LoggerInterface $logger, string $webhookToken)
    {
        $this->webhookToken = str_replace("'", '', $webhookToken);
    }

    public function checkToken(string $webhookToken)
    {
        if ($webhookToken !== $this->webhookToken) {
            $this->logger->error('Incorrect webhook token', ['w1' => $webhookToken, 'w2' => $this->webhookToken]);
            throw new AccessDeniedException();
        }
    }
}
