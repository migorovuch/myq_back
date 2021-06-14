<?php

namespace App\Model\Manager;

use App\Entity\Schedule;
use App\Entity\SpecialHours;
use App\Model\DTO\SpecialHours\SpecialHoursDTO;
use App\Model\DTO\SpecialHours\SpecialHoursFindDTO;
use App\Repository\SpecialHoursRepository;
use App\Security\SpecialHoursVoter;
use App\Util\DTOExporter\DTOExporterInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class SpecialHoursManager extends AbstractCRUDManager implements SpecialHoursManagerInterface
{
    protected SerializerInterface $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        SpecialHoursRepository $specialHoursRepository,
        Security $security,
        DTOExporterInterface $specialHoursDtoExporter,
        SerializerInterface $serializer
    ) {
        parent::__construct($entityManager, $specialHoursRepository, $security, $specialHoursDtoExporter);
        $this->serializer = $serializer;
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
                    $result[] = $entity;
                }
            }
            $this->entityManager->flush();
        } catch (AccessDeniedException $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $result;
    }

    /**
     * @param Schedule $schedule
     * @param DateTime $start
     * @param DateTime $end
     * @return bool
     */
    public function checkScheduleAvailability(Schedule $schedule, DateTime $start, DateTime $end): bool
    {
        $specialHours = $this->findByDTO(
            new SpecialHoursFindDTO(null, $schedule, null, null, null, $start, $end, true)
        );
        /** @var SpecialHours $item */
        foreach ($specialHours as $item) {
            if (
                $item->getRepeatCondition() === SpecialHours::REPEAT_EVERY_DAY ||
                ($item->getRepeatCondition() === SpecialHours::REPEAT_ONCE_A_WEAK && $start->format('w') == $item->getRepeatDay()) ||
                ($item->getRepeatCondition() === SpecialHours::REPEAT_ONCE_A_MONTH && $start->format('d') == $item->getRepeatDate()->format('d')) ||
                ($item->getRepeatCondition() === SpecialHours::REPEAT_ONCE_A_YEAR && $start->format('md') == $item->getRepeatDate()->format('md'))
            ) {
                foreach ($item->getRanges() as $range) {
                    $comparingDateStart = clone $start;
                    list($hour, $minute) = explode(':', $range['from']);
                    $comparingDateStart->setTime($hour, $minute);
                    $comparingDateEnd = clone $end;
                    list($hour, $minute) = explode(':', $range['to']);
                    $comparingDateEnd->setTime($hour, $minute);
                    if ($start >= $comparingDateStart && $end <= $comparingDateEnd) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
