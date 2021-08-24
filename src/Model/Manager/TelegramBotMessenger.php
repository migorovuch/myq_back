<?php

namespace App\Model\Manager;

use TelegramBot\Api\BotApi;

class TelegramBotMessenger implements BotMessengerInterface
{
    /**
     * TelegramBotMessenger constructor.
     */
    public function __construct(protected BotApi $botApi)
    {}

    public function sendMessage(int $chatId, string $message)
    {
        $this->botApi->sendMessage($chatId, $message);
    }
}
