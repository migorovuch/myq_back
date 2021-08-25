<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Entity\Company;
use App\Entity\CompanyChat;
use App\Exception\BotRequestHandler\ActionNotFoundException;
use App\Exception\BotRequestHandler\RequiredPayloadNotFoundException;
use App\Exception\EntryNotFoundException;
use App\Model\DTO\CompanyChat\CompanyChatDTO;
use App\Security\TelegramWebhookTokenChecker;
use Symfony\Contracts\Translation\TranslatorInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class TelegramBotRequestHandler implements BotRequestHandlerInterface
{

    protected array $appLocales = [];

    /**
     * TelegramBotRequestHandler constructor.
     * @param CompanyChatManagerInterface $companyChatManager
     * @param BookingManagerInterface $bookingManager
     * @param CompanyManagerInterface $companyManager
     * @param TelegramWebhookTokenChecker $telegramWebhookTokenChecker
     * @param TranslatorInterface $translator
     * @param BotApi $botApi
     * @param string $appLocales
     */
    public function __construct(
        protected CompanyChatManagerInterface $companyChatManager,
        protected BookingManagerInterface $bookingManager,
        protected CompanyManagerInterface $companyManager,
        protected TelegramWebhookTokenChecker $telegramWebhookTokenChecker,
        protected TranslatorInterface $translator,
        protected BotApi $botApi,
        string $appLocales
    ) {
        $this->appLocales = explode(',', $appLocales);
    }

    public function handleRequest(string $webhookToken, int $chatId, string $message)
    {
        $this->telegramWebhookTokenChecker->checkToken($webhookToken);
        /** @var CompanyChat|null $companyChat */
        $companyChat = $this->companyChatManager->findOneBy(['chatId' => $chatId]);
        $locale = $companyChat ? $companyChat->getChatLanguage() : CompanyChat::DEFAULT_CHAT_LANGUAGE;
        if (!$companyChat) {
            if (in_array($message, $this->appLocales) && count($this->appLocales) > 1) {
                $companyChat = $this->companyChatManager->create(
                    new CompanyChatDTO(null, $chatId, $message)
                );
                $this->askCompanyAccessToken($chatId, $locale);
            } elseif (count($this->appLocales) > 1) {
                $keyboard = new ReplyKeyboardMarkup([$this->appLocales], true); // true for one-time keyboard
                $this->botApi->sendMessage(
                    $chatId,
                    $this->translator->trans('Select language', [],
                        'messages',
                        CompanyChat::DEFAULT_CHAT_LANGUAGE),
                    null,
                    false,
                    null,
                    $keyboard
                );
            } elseif ($this->isMessageContainsAccessToken($message)) {
                $this->setCompanyChatCompany($chatId, $message);
            } else {
                $companyChat = $this->companyChatManager->create(
                    new CompanyChatDTO(null, $chatId, null)
                );
                $this->askCompanyAccessToken($chatId, $locale);
            }
        } elseif (!$companyChat->getCompany()) {
            if ($this->isMessageContainsAccessToken($message)) {
                $companyChat = $this->setCompanyChatCompany($chatId, $message, $companyChat);
            } else {
                $this->askCompanyAccessToken($chatId, $locale);
            }
        } elseif (in_array($message, $this->appLocales) && count($this->appLocales) > 1) {
            /** @var CompanyChat $companyChat */
            $companyChat = $this->companyChatManager->change(
                $companyChat->getId(),
                new CompanyChatDTO($companyChat->getCompany(), $chatId, $message)
            );
            $this->botApi->sendMessage(
                $chatId,
                $this->translator->trans(
                    'Chat language has been changed',
                    [],
                    'messages',
                    $companyChat->getChatLanguage()
                )
            );
        } else {
            switch ($message) {
                case $this->companyChatManager->getConfirmBookingActionString($companyChat):
                    $this->approveBooking($companyChat);
                    break;
                case $this->companyChatManager->getDeclineBookingActionString($companyChat):
                    $this->cancelBooking($companyChat);
                    break;
                default:
                    throw new ActionNotFoundException(
                        $this->translator->trans(
                            'Action "%action%" not found',
                            ['%action%' => $message],
                            'messages',
                            $companyChat->getChatLanguage()
                        )
                    );
            }
        }
    }

    protected function askCompanyAccessToken(string $chatId, string $locale)
    {
        $this->botApi->sendMessage(
            $chatId,
            $this->translator->trans(
                'Provide company Access Token (Company Settings -> Access Token)',
                [],
                'messages',
                $locale
            )
        );
    }

    protected function setCompanyChatCompany(string $chatId, string $accessToke, CompanyChat $companyChat = null)
    {
        /** @var Company $company */
        $company = $this->companyManager->findOneBy(['accessToken' => $accessToke]);
        if (!$company) {
            throw new EntryNotFoundException();
        }
        if (!$companyChat) {
            $companyChat = $this->companyChatManager->create(
                new CompanyChatDTO($company, $chatId, null)
            );
        } else {
            $companyChat = $this->companyChatManager->change(
                $companyChat->getId(),
                new CompanyChatDTO($company, $chatId, null)
            );
        }
        $this->botApi->sendMessage(
            $chatId,
            $this->translator->trans(
                'Company successfully added. Now you will receive notifications about company events.',
                [],
                'messages',
                $companyChat ? $companyChat->getChatLanguage() : CompanyChat::DEFAULT_CHAT_LANGUAGE
            )
        );

        return $companyChat;
    }

    protected function isMessageContainsAccessToken($message): bool
    {
        return str_contains($message, self::ACTION_COMPANY.self::MESSAGE_PAYLOAD_DELIMITER);
    }

    protected function approveBooking(CompanyChat $companyChat)
    {
        if (empty($companyChat->getPayload())) {
            throw new RequiredPayloadNotFoundException();
        }
        $booking = $this->bookingManager->changeBookingStatus($companyChat->getPayload(), Booking::STATUS_ACCEPTED);
        $this->botApi->sendMessage(
            $companyChat->getChatId(),
            $this->translator->trans(
                'Booking %bookingTime% approved',
                ['%bookingTime%' => $booking->getHumanReadableTime()],
                'messages',
                $companyChat->getChatLanguage()
            )
        );
    }

    protected function cancelBooking(CompanyChat $companyChat)
    {
        if (empty($companyChat->getPayload())) {
            throw new RequiredPayloadNotFoundException();
        }
        $booking = $this->bookingManager->changeBookingStatus($companyChat->getPayload(), Booking::STATUS_DECLINED);
        $this->botApi->sendMessage(
            $companyChat->getChatId(),
            $this->translator->trans(
                'Booking %bookingTime% cancelled',
                ['%bookingTime%' => $booking->getHumanReadableTime()],
                'messages',
                $companyChat->getChatLanguage()
            )
        );
    }
}
