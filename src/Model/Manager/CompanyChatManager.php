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
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class CompanyChatManager extends AbstractCRUDManager implements CompanyChatManagerInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        CompanyChatRepository $companyChatRepository,
        Security $security,
        DTOExporterInterface $companyChatDtoExporter,
        protected TranslatorInterface $translator,
        protected BotApi $botApi
    ) {
        parent::__construct($entityManager, $companyChatRepository, $security, $companyChatDtoExporter);
    }

    public function getConfirmBookingActionString(CompanyChat $companyChat): string
    {
        return $this->translator->trans('Confirm booking', [], 'messages', $companyChat->getChatLanguage());
    }

    public function getDeclineBookingActionString(CompanyChat $companyChat): string
    {
        return $this->translator->trans('Decline booking', [], 'messages', $companyChat->getChatLanguage());
    }

    public function sendNewBookingNotification(Company $company, Booking $booking)
    {
        $chatList = $this->findByDTO(new CompanyChatFindDTO($company));
        /** @var CompanyChat $companyChat */
        foreach ($chatList as $companyChat) {
            $keyboard = new ReplyKeyboardMarkup(
                [
                    $this->getConfirmBookingActionString($companyChat),
                    $this->getDeclineBookingActionString($companyChat),
                ],
                true
            ); // true for one-time keyboard
            $this->botApi->sendMessage(
                $companyChat->getChatId(),
                $this->translator->trans(
                    'You have a new booking',
                    [],
                    'messages',
                    $companyChat->getChatLanguage()
                ).' - '.$booking->getHumanReadableTime(),
                null,
                false,
                null,
                $keyboard
            );
            $this->change($companyChat->getId(), new CompanyChatDTO(null, null, null, $booking->getId()));
        }
    }
}
