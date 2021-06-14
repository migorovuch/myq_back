<?php

namespace App\Model\Manager;

use App\Entity\User;
use App\Exception\UserHasNoCompanyException;
use App\Model\DTO\DTOInterface;
use App\Model\DTO\Schedule\ScheduleFindDTO;
use App\Model\Model\EntityInterface;
use App\Repository\ScheduleRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Security;

/**
 * Class ScheduleManager
 */
class ScheduleManager extends AbstractCRUDManager implements ScheduleManagerInterface
{
    /**
     * ScheduleManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param ScheduleRepository $scheduleRepository
     * @param Security $security
     * @param DTOExporterInterface $scheduleDtoExporter
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ScheduleRepository $scheduleRepository,
        Security $security,
        DTOExporterInterface $scheduleDtoExporter,
    ) {
        parent::__construct($entityManager, $scheduleRepository, $security, $scheduleDtoExporter);
    }

    /**
     * @param ScheduleFindDTO $data
     * @return array
     */
    public function findPublicByDTO(ScheduleFindDTO $data): array
    {
        if (!(
            $this->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY) &&
            $data->getCompany() &&
            $this->security->getUser()->getId() == $data->getCompany()->getUser()->getId()
        )) {
            $data = new ScheduleFindDTO(
                $data->getId(),
                $data->getCompany(),
                $data->getName(),
                true,
                $data->getSort(),
                $data->getPage(),
                $data->getCondition()
            );
        }

        return $this->findByDTO($data);
    }

    /**
     * @param EntityInterface $entity
     * @param DTOInterface $dto
     * @param bool $setNullProperty
     * @return EntityInterface
     */
    protected function prepareEntity(
        EntityInterface $entity,
        DTOInterface $dto,
        bool $setNullProperty = true
    ): EntityInterface {
        $entity = parent::prepareEntity($entity, $dto, $setNullProperty);
        /** @var User $user */
        $user = $this->security->getUser();
        $company = $user->getFirstCompany();
        if (!$company) {
            throw new UserHasNoCompanyException();
        }

        return $entity->setCompany($company);
    }
}
