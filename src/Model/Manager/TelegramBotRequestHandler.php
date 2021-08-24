<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Exception\BotRequestHandler\ActionNotFoundException;
use App\Exception\BotRequestHandler\RequiredPayloadNotFoundException;
use App\Security\TelegramWebhookTokenChecker;
use Symfony\Contracts\Translation\TranslatorInterface;

class TelegramBotRequestHandler implements BotRequestHandlerInterface
{

    const MESSAGE_PAYLOAD_DELIMITER = ':';

    const ACTION_CONFIRM_BOOKING = 'confirm_booking';
    const ACTION_DECLINE_BOOKING = 'decline_booking';

    /**
     * TelegramBotRequestHandler constructor.
     * @param BookingManagerInterface $bookingManager
     * @param TelegramWebhookTokenChecker $telegramWebhookTokenChecker
     */
    public function __construct(
        protected BookingManagerInterface $bookingManager,
        protected TelegramWebhookTokenChecker $telegramWebhookTokenChecker,
        protected TranslatorInterface $translator
    ) {}

    public function handleRequest(string $webhookToken, int $chatId, string $message)
    {
        $this->telegramWebhookTokenChecker->checkToken($webhookToken);
        $payload = explode(self::MESSAGE_PAYLOAD_DELIMITER, $message);
        $action = array_shift($payload);
        switch ($action) {
            case self::ACTION_CONFIRM_BOOKING:
                if (!isset($payload[0])) {
                    throw new RequiredPayloadNotFoundException();
                }
                $this->bookingManager->changeBookingStatus($payload[0], Booking::STATUS_ACCEPTED);
                break;
            case self::ACTION_DECLINE_BOOKING:
                if (!isset($payload[0])) {
                    throw new RequiredPayloadNotFoundException();
                }
                $this->bookingManager->changeBookingStatus($payload[0], Booking::STATUS_DECLINED);
                break;
            default:
                throw new ActionNotFoundException($this->translator->trans('Action %action% not found', ['%action%' => $action]));
        }
    }
}
