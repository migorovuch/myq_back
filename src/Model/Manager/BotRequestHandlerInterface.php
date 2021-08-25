<?php

namespace App\Model\Manager;

interface BotRequestHandlerInterface
{

    const MESSAGE_PAYLOAD_DELIMITER = ':';

    const ACTION_COMPANY = 'cmp';
    const ACTION_CONFIRM_BOOKING = 'Confirm booking';
    const ACTION_DECLINE_BOOKING = 'Decline booking';

    public function handleRequest(string $webhookToken, int $chatId, string $message);
}
