<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Entity\Schedule;
use App\Entity\User;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\Booking\BookingDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Model\DTO\DTOInterface;
use App\Model\Model\EntityInterface;
use App\Repository\BookingRepository;
use App\Security\AbstractVoter;
use App\Security\BookingVoter;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class BookingManager extends AbstractCRUDManager implements BookingManagerInterface
{

    /**
     * BookingManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param BookingRepository $bookingRepository
     * @param Security $security
     * @param DTOExporterInterface $bookingDtoExporter
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        BookingRepository $bookingRepository,
        Security $security,
        DTOExporterInterface $bookingDtoExporter,
    ) {
        parent::__construct($entityManager, $bookingRepository, $security, $bookingDtoExporter);
    }

    /**
     * @param DTOInterface $data
     *
     * @return EntityInterface
     */
    public function create(DTOInterface $data)
    {
        $entityName = $this->entityRepository->getClassName();
        $entity = new $entityName();
        $entity = $this->prepareEntity($entity, $data);
        switch ($entity->getSchedule()->getAcceptBookingCondition()) {
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
        $entity->setStatus($status);
        $this->denyAccessUnlessGranted(BookingVoter::CREATE, $entity);
        $this->save($entity);

        return $entity;
    }

    /**
     * @param Booking $entity
     * @param BookingDTO $dto
     * @param bool $setNullProperty
     * @return EntityInterface
     */
    protected function prepareEntity(EntityInterface $entity, DTOInterface $dto, bool $setNullProperty = true): EntityInterface
    {
        $originalSchedule = $entity->getSchedule();
        $originalStatus = $entity->getStatus();
        /** @var Booking $entity */
        $entity = parent::prepareEntity($entity, $dto, $setNullProperty);
        if ($dto->getSchedule()) {
            $entity->setSchedule($originalSchedule ?? $dto->getSchedule());
        }
        if ($this->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
            /** @var User $currentUser */
            $currentUser = $this->security->getUser();
            $entity->setUser($currentUser);
            if (!$entity->getUserName()) {
                $entity->setUserName($currentUser->getUsername());
            }
            if (!$entity->getUserPhone()) {
                $entity->setUserPhone($currentUser->getPhone());
            }
        } else {
            $entity->setUser(null);
        }
        $entity->setStatus($originalStatus);

        return $entity;
    }
}
