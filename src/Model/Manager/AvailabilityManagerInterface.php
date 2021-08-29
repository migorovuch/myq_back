<?php

namespace App\Model\Manager;

use App\Model\DTO\Availability\AvailabilityFindDTO;

/**
 * Interface AvailabilityManagerInterface.
 */
interface AvailabilityManagerInterface
{
    /**
     * @param AvailabilityFindDTO $data
     *
     * @return array
     */
    public function findByDTO(AvailabilityFindDTO $data);
}
