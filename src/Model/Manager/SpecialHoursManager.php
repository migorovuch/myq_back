<?php

namespace App\Model\Manager;

use App\Model\DTO\SpecialHours\SpecialHoursDTO;
use App\Model\DTO\SpecialHours\SpecialHoursFindDTO;
use App\Repository\SpecialHoursRepository;
use App\Security\SpecialHoursVoter;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class SpecialHoursManager extends AbstractCRUDManager implements SpecialHoursManagerInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        SpecialHoursRepository $specialHoursRepository,
        Security $security,
        DTOExporterInterface $specialHoursDtoExporter,
    ) {
        parent::__construct($entityManager, $specialHoursRepository, $security, $specialHoursDtoExporter);
    }

    /**
     * @param SpecialHoursFindDTO $data
     * @return array
     */
    public function findPublicByDTO(SpecialHoursFindDTO $data): array
    {
        if (!(
            $this->security->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY) &&
            $data->getSchedule() &&
            $this->security->getUser()->getId() == $data->getSchedule()->getCompany()->getUser()->getId()
        )) {
            $data = new SpecialHoursFindDTO(
                $data->getId(),
                $data->getSchedule(),
                $data->getRepeatCondition(),
                $data->getRepeatDay(),
                $data->getFilterRepeatDate(),
                $data->getFilterFrom(),
                $data->getFilterTo(),
                true,
                $data->getSort(),
                $data->getPage(),
                $data->getCondition()
            );
        }

        return $this->findByDTO($data);
    }

    /**
     * @param array $list
     * @return array
     */
    public function updateList(array $list): array
    {
        $result = [];
        try {
            /** @var SpecialHoursDTO $specialHoursDTO */
            foreach ($list as $specialHoursDTO) {
                if ($specialHoursDTO->getId()) {
                    $entity = $this->find($specialHoursDTO->getId());
                    $this->denyAccessUnlessGranted(SpecialHoursVoter::UPDATE, $entity);
                    $entity = $this->DTOExporter->exportDTO($entity, $specialHoursDTO, false);
                    $this->entityManager->persist($entity);
                    $result[] = $entity;
                } else {
                    $entityName = $this->entityRepository->getClassName();
                    $entity = new $entityName();
                    $entity = $this->DTOExporter->exportDTO($entity, $specialHoursDTO);
                    $this->denyAccessUnlessGranted(SpecialHoursVoter::CREATE, $entity);
                    $this->entityManager->persist($entity);
                }
            }
            $this->entityManager->flush();
        } catch (AccessDeniedException $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $result;
    }
}
