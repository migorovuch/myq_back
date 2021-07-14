<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Entity\Schedule;
use App\Entity\User;
use App\Exception\AccessDeniedException;
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

class BookingManager extends AbstractCRUDManager implements BookingManagerInterface
{
    protected CompanyClientManagerInterface $companyClientManager;

    /**
     * BookingManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param BookingRepository $bookingRepository
     * @param Security $security
     * @param DTOExporterInterface $bookingDtoExporter
     * @param CompanyClientManagerInterface $companyClientManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        BookingRepository $bookingRepository,
        Security $security,
        DTOExporterInterface $bookingDtoExporter,
        CompanyClientManagerInterface $companyClientManager
    ) {
        parent::__construct($entityManager, $bookingRepository, $security, $bookingDtoExporter);
        $this->companyClientManager = $companyClientManager;
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
            $data->getSchedule(),
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
        switch ($entity->getSchedule()->getAcceptBookingCondition()) {
            case Schedule::ACCEPT_BOOKING_DO_NOTHING:
                $status = Booking::STATUS_NEW;
                break;
            case Schedule::ACCEPT_BOOKING_ACCEPT_ALL:
                $status = Booking::STATUS_ACCEPTED;
                break;
            case Schedule::ACCEPT_BOOKING_ACCEPT_APPROVED_USERS; // TODO
            case Schedule::ACCEPT_BOOKING_ACCEPT_AFTER_PAY_ADVANCE; // TODO
            case Schedule::ACCEPT_BOOKING_DECLINE_ALL;
                $status = Booking::STATUS_DECLINED;
                break;
            default:
                $status = Booking::STATUS_DECLINED;
        }
        $currentUser = null;
        if ($this->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
            /** @var User $currentUser */
            $currentUser = $this->security->getUser();
            if (!$currentUser->getPhone()) {
                $currentUser->setPhone($data->getUserPhone());
                $this->save($currentUser);
            }
            if (
                $data->isNewClient() &&
                $data->getSchedule()->getCompany()->getUser()->getId() === $currentUser->getId()
            ) {
                $currentUser = null;
            }
        }
        $companyClientDTO = new CompanyClientDTO(
            $data->getClient(),
            $currentUser,
            $data->getUserName(),
            $data->getUserPhone(),
            $data->getSchedule()->getCompany()
        );
        $companyClient = $this->companyClientManager->getByDTO($companyClientDTO);
        $entity
            ->setStatus($status)
            ->setClient($companyClient)
            ->setTitle($entity->getUserName());
        $this->denyAccessUnlessGranted(BookingVoter::CREATE, $entity);
        $this->save($entity);

        return $entity;
    }
}
