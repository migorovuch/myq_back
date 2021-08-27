<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Entity\Company;
use App\Entity\CompanyChat;
use App\Model\DTO\CompanyChat\CompanyChatDTO;
use App\Model\DTO\CompanyChat\CompanyChatFindDTO;
use App\Repository\CompanyChatRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class CompanyChatManager extends AbstractCRUDManager implements CompanyChatManagerInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        CompanyChatRepository $companyChatRepository,
        Security $security,
        DTOExporterInterface $companyChatDtoExporter,
        protected TranslatorInterface $translator,
        protected BotApi $botApi,
        protected string $appUrl
    ) {
        parent::__construct($entityManager, $companyChatRepository, $security, $companyChatDtoExporter);
    }

    public function sendNewBookingNotification(Company $company, Booking $booking)
    {
        $chatList = $this->findByDTO(new CompanyChatFindDTO($company));
        /** @var CompanyChat $companyChat */
        foreach ($chatList as $companyChat) {
            $keyboard = new InlineKeyboardMarkup(
                [
                    [
                        [
                            'text' => $this->translator->trans('Confirm', [], 'messages',
                                $companyChat->getChatLanguage()),
                            'callback_data' => BotRequestHandlerInterface::ACTION_CONFIRM_BOOKING.BotRequestHandlerInterface::MESSAGE_PAYLOAD_DELIMITER.$booking->getId()
                        ],
                        [
                            'text' => $this->translator->trans('Decline', [], 'messages',
                                $companyChat->getChatLanguage()),
                            'callback_data' => BotRequestHandlerInterface::ACTION_DECLINE_BOOKING.BotRequestHandlerInterface::MESSAGE_PAYLOAD_DELIMITER.$booking->getId()
                        ],
                    ],
                ]
            );
            $bookingsLink = $this->appUrl.'#/company/bookings';
            $this->botApi->sendMessage(
                $companyChat->getChatId(),
                $this->translator->trans(
                    'You have a new booking',
                    [],
                    'messages',
                    $companyChat->getChatLanguage()
                ).' - '.$booking->getSchedule()->getName().' '.$booking->getHumanReadableTime().PHP_EOL.$bookingsLink,
                null,
                false,
                null,
                $keyboard
            );
            $this->change($companyChat->getId(), new CompanyChatDTO(null, null, null, $booking->getId()));
        }
    }
}
