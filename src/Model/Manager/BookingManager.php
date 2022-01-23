<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Entity\CompanyClient;
use App\Entity\Schedule;
use App\Entity\User;
use App\Exception\AccessDeniedException;
use App\Exception\EntryNotFoundException;
use App\Exception\UnauthorizedBookingException;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\Booking\BookingDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Model\DTO\CompanyClient\CompanyClientDTO;
use App\Model\DTO\DTOInterface;
use App\Model\Model\EntityInterface;
use App\Repository\BookingRepository;
use App\Security\BookingVoter;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class BookingManager extends AbstractCRUDManager implements BookingManagerInterface
{
    /**
     * BookingManager constructor.
     *
     * @param EntityManagerInterface        $entityManager
     * @param BookingRepository             $bookingRepository
     * @param Security                      $security
     * @param DTOExporterInterface          $bookingDtoExporter
     * @param TranslatorInterface           $translator
     * @param CompanyClientManagerInterface $companyClientManager
     * @param CompanyChatManagerInterface   $companyChatManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        BookingRepository $bookingRepository,
        Security $security,
        DTOExporterInterface $bookingDtoExporter,
        protected TranslatorInterface $translator,
        protected CompanyClientManagerInterface $companyClientManager,
        protected CompanyChatManagerInterface $companyChatManager
    ) {
        parent::__construct($entityManager, $bookingRepository, $security, $bookingDtoExporter);
    }

    /**
     * @param BookingFindDTO $data
     *
     * @return array|mixed
     */
    public function findByDTO(AbstractFindDTO $data)
    {
        $currentUser = $this->security->getUser();
        if (!(
            (
                $this->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY) &&
                (
                    ($data->getCompany() && $data->getCompany()->getUser()->getId() === $currentUser->getId()) ||
                    ($data->getSchedule() && $data->getSchedule()->getCompany()->getUser()->getId() === $currentUser->getId()) ||
                    ($data->getUser() && $data->getUser()->getId() === $currentUser->getId()) ||
                    $this->security->isGranted(User::ROLE_ADMIN)
                )
            ) ||
            (
                !$this->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY) &&
                (
                    ($data->getClient() && !$data->getClient()->getUser()) ||
                    !empty($data->getClients())
                )
            )
        )) {
            throw new AccessDeniedException();
        }

        return parent::findByDTO($data);
    }

    /**
     * {@inheritDoc}
     */
    public function buildMyBookingFindDTO(AbstractFindDTO $data): BookingFindDTO
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        $client = $data->getClient();
        if (
            !$currentUser &&
            (
                ($client && $client->getUser()) ||
                (!$client && empty($data->getClients()))
            )
        ) {
            throw new AccessDeniedException();
        }
        // update client-user relation
        if ($currentUser && ($client = $data->getClient()) && !$client->getUser()) {
            $client->setUser($currentUser);
            $this->save($client);
        }
        if (!empty($data->getClients())) {
            $clients = $this->companyClientManager->getListByIDs($data->getClients());
            /** @var CompanyClient $client */
            foreach ($clients as $client) {
                if ($client->getUser() && !$currentUser) {
                    throw new AccessDeniedException();
                }
            }
        }

        return new BookingFindDTO(
            $data->getId(),
            $data->getStatus(),
            $data->getScheduleName(),
            $data->getSchedule(),
            $data->getCompanyName(),
            $data->getCompany(),
            $data->getFilterFrom(),
            $data->getFilterTo(),
            $data->getTitle(),
            $data->getCustomerComment(),
            $data->getClient(),
            $data->getClients(),
            $currentUser,
            $data->getUserName(),
            $data->getUserPhone(),
            $data->getSort(),
            $data->getPage(),
            $data->getCondition()
        );
    }

    /**
     * @param BookingDTO $data
     *
     * @return EntityInterface
     */
    public function create(DTOInterface $data)
    {
        $entityName = $this->entityRepository->getClassName();
        $entity = new $entityName();
        /** @var Booking $entity */
        $entity = $this->prepareEntity($entity, $data);
        if (
            $entity->getClient() &&
            $entity->getClient()->getCompany()->getId() !== $data->getSchedule()->getCompany()->getId()
        ) {
            $entity->setClient(null);
        }
        if (
            Schedule::BOOKING_CONDITION_AUTHORIZED_USERS === $entity->getSchedule()->getBookingCondition() &&
            !$this->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)
        ) {
            throw new UnauthorizedBookingException($this->translator->trans('This booking is only available for authorized users. Please, sign in.'));
        }
        switch ($entity->getSchedule()->getAcceptBookingCondition()) {
            case Schedule::ACCEPT_BOOKING_DO_NOTHING:
                $status = Booking::STATUS_NEW;
                break;
            case Schedule::ACCEPT_BOOKING_ACCEPT_ALL:
                $status = Booking::STATUS_ACCEPTED;
                break;
            case Schedule::ACCEPT_BOOKING_ACCEPT_APPROVED_USERS:
                if (
                    $entity->getClient() &&
                    CompanyClient::STATUS_ON === $entity->getClient()->getStatus()
                ) {
                    $status = Booking::STATUS_ACCEPTED;
                } else {
                    $status = Booking::STATUS_NEW;
                }
                break;
            case Schedule::ACCEPT_BOOKING_ACCEPT_AFTER_PAY_ADVANCE: // TODO
            case Schedule::ACCEPT_BOOKING_DECLINE_ALL:
                $status = Booking::STATUS_DECLINED;
                break;
            default:
                $status = Booking::STATUS_NEW;
        }
        $currentUser = $companyClient = null;
        if ($this->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
            /** @var User $currentUser */
            $currentUser = $this->security->getUser();
            // Company manager can create new booking as new client
            if (
                $data->isNewClient() &&
                $data->getSchedule()->getCompany()->getUser()->getId() === $currentUser->getId()
            ) {
                $currentUser = null;
            } else {
                $companyClient = $this->companyClientManager->findOneBy([
                    'user' => $currentUser,
                    'company' => $data->getSchedule()->getCompany(),
                    'deleted' => CompanyClient::STATE_NOT_DELETED,
                ]);
            }
            // update user phone if it wasn't configured
            if ($currentUser && !$currentUser->getPhone()) {
                $currentUser->setPhone($data->getUserPhone());
                $this->save($currentUser);
            }
        }
        if (
            (!$companyClient && !$entity->getClient()) ||
            (
                !$companyClient &&
                $entity->getClient() &&
                !($companyClient = $this->companyClientManager->checkExistingClientForBooking($entity->getClient(), $currentUser))
            )
        ) {
            if (!$currentUser) {
                // find client by phone number if client id or any details wasn't provided
                $companyClient = $this->companyClientManager->findOneBy([
                    'company' => $data->getSchedule()->getCompany(),
                    'phone' => $data->getUserPhone(),
                    'deleted' => CompanyClient::STATE_NOT_DELETED,
                ]);
            }
            if (!$companyClient) {
                $companyClientDTO = new CompanyClientDTO(
                    $currentUser,
                    $data->getUserName(),
                    $data->getUserPhone(),
                    $data->getSchedule()->getCompany(),
                    Schedule::ACCEPT_BOOKING_ACCEPT_APPROVED_USERS === $entity->getSchedule(
                    )->getAcceptBookingCondition() ?
                        CompanyClient::STATUS_OFF :
                        CompanyClient::STATUS_ON
                );
                $companyClient = $this->companyClientManager->create($companyClientDTO);
            }
        } elseif (
            $companyClient->getName() != $data->getUserName() ||
            $companyClient->getPhone() != $data->getUserPhone()
        ) {
            $companyClient
                ->setName($data->getUserName())
                ->setPhone($data->getUserPhone());
            $this->save($companyClient);
        }
        $entity
            ->setStatus($status)
            ->setClient($companyClient);
        $this->denyAccessUnlessGranted(BookingVoter::CREATE, $entity);
        $this->save($entity);
        $this->companyChatManager->sendNewBookingNotification($data->getSchedule()->getCompany(), $entity);

        return $entity;
    }

    /**
     * @param string $companyId
     * @param string $bookingId
     * @param int    $status
     *
     * @return Booking
     */
    public function changeBookingStatus(string $companyId, string $bookingId, int $status)
    {
        $booking = $this->entityRepository->findCompanyBooking($companyId, $bookingId);
        if (!$booking) {
            throw new EntryNotFoundException($this->translator->trans('Booking not found'));
        }
        $booking->setStatus($status);
        $this->save($booking);

        return $booking;
    }
}
