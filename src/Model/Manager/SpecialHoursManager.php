<?php

namespace App\Model\Manager;

use App\Entity\Schedule;
use App\Entity\SpecialHours;
use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\SpecialHours\SpecialHoursDTO;
use App\Model\DTO\SpecialHours\SpecialHoursFindDTO;
use App\Repository\SpecialHoursRepository;
use App\Security\SpecialHoursVoter;
use App\Util\DTOExporter\DTOExporterInterface;
use DateInterval;
use DatePeriod;
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

    /**
     * @param array $rangesArray1
     * @param array $rangesArray2
     * @return array
     */
    public function addRanges(array $rangesArray1, array $rangesArray2): array
    {
        $result = [];
        foreach ($rangesArray1 as $range) {
            $result = $this->addRange($result, $range);
        }
        foreach ($rangesArray2 as $range) {
            $result = $this->addRange($result, $range);
        }

        return $this->mergeRanges($result);
    }

    /**
     * @param array $ranges
     * @return array
     */
    protected function sortRanges(array $ranges): array
    {
        usort($ranges, function ($range1, $range2) {
            $from1 = date('H:i', strtotime($range1['from']));
            $from2 = date('H:i', strtotime($range2['from']));
            if ($from1 == $from2) {
                return 0;
            }
            return ($from1 < $from2) ? -1 : 1;
        });

        return $ranges;
    }

    /**
     * @param array $rangesArray
     * @param array $range
     * @return array
     */
    protected function addRange(array $rangesArray, array $range): array
    {
        foreach ($rangesArray as $range2) {
            if (
                date('H:i', strtotime($range['to'])) < date('H:i', strtotime($range2['to'])) &&
                date('H:i', strtotime($range['to'])) >= date('H:i', strtotime($range2['from']))
            ) {
                $range['to'] = $range2['from'];
            }
            if (
                date('H:i', strtotime($range['from'])) > date('H:i', strtotime($range2['from'])) &&
                date('H:i', strtotime($range['from'])) <= date('H:i', strtotime($range2['to']))
            ) {
                $range['from'] = $range2['to'];
            }
            if (
                date('H:i', strtotime($range['from'])) >= date('H:i', strtotime($range['to']))
            ) {
                return $rangesArray;
            }
        }
        $rangesArray[] = $range;

        return $rangesArray;
    }

    /**
     * @param array $arrayRanges
     * @return array
     */
    protected function mergeRanges(array $arrayRanges): array
    {
        $result = [];
        foreach ($arrayRanges as $range1) {
            $range1From = date('H:i', strtotime($range1['from']));
            $range1To = date('H:i', strtotime($range1['to']));
            $changed = false;
            foreach ($result as &$range2) {
                $range2From = date('H:i', strtotime($range2['from']));
                $range2To = date('H:i', strtotime($range2['to']));
                if ($range2From > $range1From && $range2To >= $range1From) {
                    $range2['from'] = $range1['from'];
                    $changed = true;
                }
                if ($range2To < $range1To && $range2To >= $range1From) {
                    $range2['to'] = $range1['to'];
                    $changed = true;
                }
            }
            if (!$changed) {
                $result[] = $range1;
            }
        }

        return $result;
    }

    /**
     * @param AbstractFindDTO $data
     * @return array
     */
    public function getPeriodAvailability(AbstractFindDTO $data): array
    {
        $specialHours = $this->entityRepository->findByDTO($data);

        $dailySpecialHours = $weaklySpecialHours = $monthlySpecialHours = $yearlySpecialHours = [];
        /** @var SpecialHours $specialHoursItem */
        foreach ($specialHours as $specialHoursItem) {
            switch ($specialHoursItem->getRepeatCondition()) {
                case SpecialHours::REPEAT_EVERY_DAY:
                    $dailySpecialHours[] = $specialHoursItem->getRanges();
                    break;
                case SpecialHours::REPEAT_ONCE_A_WEAK:
                    if (!isset($weaklySpecialHours[$specialHoursItem->getRepeatDay()])) {
                        $weaklySpecialHours[$specialHoursItem->getRepeatDay()] = [];
                    }
                    $weaklySpecialHours[$specialHoursItem->getRepeatDay()] = array_merge(
                        $weaklySpecialHours[$specialHoursItem->getRepeatDay()],
                        $specialHoursItem->getRanges()
                    );
                    break;
                case SpecialHours::REPEAT_ONCE_A_MONTH:
                    $key = $specialHoursItem->getRepeatDate()->format('d');
                    if (!isset($monthlySpecialHours[$key])) {
                        $monthlySpecialHours[$key] = [];
                    }
                    $monthlySpecialHours[$key] = array_merge($monthlySpecialHours[$key], $specialHoursItem->getRanges());
                    break;
                case SpecialHours::REPEAT_ONCE_A_YEAR:
                    $key = $specialHoursItem->getRepeatDate()->format('md');
                    if (!isset($yearlySpecialHours[$key])) {
                        $yearlySpecialHours[$key] = [];
                    }
                    $yearlySpecialHours[$key] = array_merge($yearlySpecialHours[$key], $specialHoursItem->getRanges());
                    break;
            }
        }

        $period = new DatePeriod(
            $data->getFilterFrom(),
            DateInterval::createFromDateString('1 day'),
            $data->getFilterTo()
        );
        $specialHoursPeriod = [];
        foreach ($period as $date) {
            $key = $date->format("Y-m-d");
            if (!isset($specialHoursPeriod[$key])) {
                $specialHoursPeriod[$key] = [];
            }
            foreach ($dailySpecialHours as $dailySpecialHourItem) {
                if ($dailySpecialHourItem->getStartDate() <= $date && $dailySpecialHourItem->getEndDate() >= $date) {
                    $specialHoursPeriod[$key][] = $dailySpecialHourItem;
                }
            }
            $dayKey = $date->format("w");
            if (isset($weaklySpecialHours[$dayKey])) {
                $specialHoursPeriod[$key] = $this->addRanges($specialHoursPeriod[$key], $weaklySpecialHours[$dayKey]);
            }
            $monthKey = $date->format("m");
            if (isset($monthlySpecialHours[$monthKey])) {
                $specialHoursPeriod[$key] = $this->addRanges($specialHoursPeriod[$key], $monthlySpecialHours[$monthKey]);
            }
            $yearKey = $date->format("md");
            if (isset($yearlySpecialHours[$yearKey])) {
                $specialHoursPeriod[$key] = $this->addRanges($specialHoursPeriod[$key], $yearlySpecialHours[$yearKey]);
            }
        }

        return $specialHoursPeriod;
    }
}
