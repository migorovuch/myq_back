<?php

namespace App\Model\Manager;

interface BotRequestHandlerInterface
{
    public function handleRequest(string $webhookToken, int $chatId, string $message);
}
