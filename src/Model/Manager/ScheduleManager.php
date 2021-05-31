<?php

namespace App\Model\Manager;

use App\Model\DTO\Schedule\ScheduleFindDTO;
use App\Repository\ScheduleRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Security;

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
}
