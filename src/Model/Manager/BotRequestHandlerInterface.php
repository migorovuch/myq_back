<?php

namespace App\Model\Manager;

use App\Model\DTO\BotMessageDTOInterface;

interface BotRequestHandlerInterface
{

    const MESSAGE_PAYLOAD_DELIMITER = ':';

    const ACTION_COMPANY = 'cmp';
    const ACTION_CONFIRM_BOOKING = 'confbooking';
    const ACTION_DECLINE_BOOKING = 'declbooking';

    public function handleRequest(string $webhookToken, BotMessageDTOInterface $botMessageDTO);
}
