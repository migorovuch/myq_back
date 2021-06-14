<?php

namespace App\Model\Manager;

use App\Entity\Schedule;
use DateTime;

interface SpecialHoursManagerInterface extends CRUDManagerInterface
{
    /**
     * @param array $list
     * @return array
     */
    public function updateList(array $list): array;

    /**
     * @param Schedule $schedule
     * @param DateTime $start
     * @param DateTime $end
     * @return bool
     */
    public function checkScheduleAvailability(Schedule $schedule, DateTime $start, DateTime $end): bool;
}
