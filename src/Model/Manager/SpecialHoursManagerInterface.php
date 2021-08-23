<?php

namespace App\Model\Manager;

use App\Entity\Schedule;
use App\Model\DTO\AbstractFindDTO;
use DateTime;

interface SpecialHoursManagerInterface extends CRUDManagerInterface
{
    /**
     * @param array $list
     *
     * @return array
     */
    public function updateList(array $list): array;

    /**
     * @param Schedule $schedule
     * @param DateTime $start
     * @param DateTime $end
     *
     * @return bool
     */
    public function checkScheduleAvailability(Schedule $schedule, DateTime $start, DateTime $end): bool;

    /**
     * @param array $rangesArray1
     * @param array $rangesArray2
     *
     * @return array
     */
    public function addRanges(array $rangesArray1, array $rangesArray2): array;

    /**
     * @param AbstractFindDTO $data
     *
     * @return array
     */
    public function getPeriodAvailability(AbstractFindDTO $data): array;
}
