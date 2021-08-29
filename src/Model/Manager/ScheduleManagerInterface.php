<?php

namespace App\Model\Manager;

use App\Model\DTO\Schedule\ScheduleFindDTO;

interface ScheduleManagerInterface extends CRUDManagerInterface
{
    /**
     * @param ScheduleFindDTO $data
     *
     * @return array
     */
    public function findPublicByDTO(ScheduleFindDTO $data): array;
}
