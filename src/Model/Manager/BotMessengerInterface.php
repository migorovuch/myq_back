<?php

namespace App\Model\Manager;

interface BotMessengerInterface
{
    public function sendMessage(int $chatId, string $message);
}
