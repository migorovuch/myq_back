<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Entity\CompanyClient;
use App\Entity\Schedule;
use App\Entity\User;
use App\Exception\AccessDeniedException;
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
    protected CompanyClientManagerInterface $companyClientManager;
    private TranslatorInterface $translator;

    /**
     * BookingManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param BookingRepository $bookingRepository
     * @param Security $security
     * @param DTOExporterInterface $bookingDtoExporter
     * @param CompanyClientManagerInterface $companyClientManager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        BookingRepository $bookingRepository,
        Security $security,
        DTOExporterInterface $bookingDtoExporter,
        CompanyClientManagerInterface $companyClientManager,
        TranslatorInterface $translator
    ) {
        parent::__construct($entityManager, $bookingRepository, $security, $bookingDtoExporter);
        $this->companyClientManager = $companyClientManager;
        $this->translator = $translator;
    }

    /**
     * @param BookingFindDTO $data
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
                $data->getClient() &&
                !$data->getClient()->getUser()
            )
        )) {
            throw new AccessDeniedException();
        }

        return parent::findByDTO($data);
    }

    /**
     * @inheritDoc
     */
    public function buildMyBookingFindDTO(AbstractFindDTO $data): BookingFindDTO
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        $client = $data->getClient();
        if(
            !$currentUser &&
            (
                ($client && $client->getUser()) ||
                !$client
            )
        ) {
            throw new AccessDeniedException();
        }
        // update client-user relation
        if($currentUser && ($client = $data->getClient()) && !$client->getUser()) {
            $client->setUser($currentUser);
            $this->save($client);
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
            $entity->getSchedule()->getBookingCondition() === Schedule::BOOKING_CONDITION_AUTHORIZED_USERS &&
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
            case Schedule::ACCEPT_BOOKING_ACCEPT_APPROVED_USERS;
                if (
                    $entity->getClient() &&
                    $entity->getSchedule()->getCompany()->getId() === $entity->getClient()->getCompany()->getId() &&
                    $entity->getClient()->getStatus() === CompanyClient::STATUS_ON
                ) {
                    $status = Booking::STATUS_ACCEPTED;
                } else {
                    $status = Booking::STATUS_NEW;
                }
                break;
            case Schedule::ACCEPT_BOOKING_ACCEPT_AFTER_PAY_ADVANCE; // TODO
            case Schedule::ACCEPT_BOOKING_DECLINE_ALL;
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
            $companyClientDTO = new CompanyClientDTO(
                $currentUser,
                $data->getUserName(),
                $data->getUserPhone(),
                $data->getSchedule()->getCompany(),
                $entity->getSchedule()->getAcceptBookingCondition() === Schedule::ACCEPT_BOOKING_ACCEPT_APPROVED_USERS ?
                    CompanyClient::STATUS_OFF :
                    CompanyClient::STATUS_ON
            );
            $companyClient = $this->companyClientManager->create($companyClientDTO);
        }
        $entity
            ->setStatus($status)
            ->setClient($companyClient);
        $this->denyAccessUnlessGranted(BookingVoter::CREATE, $entity);
        $this->save($entity);

        return $entity;
    }
}
