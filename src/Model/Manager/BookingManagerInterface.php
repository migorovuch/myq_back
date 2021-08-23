<?php

namespace App\Model\Manager;

use App\Model\DTO\AbstractFindDTO;
use App\Model\DTO\Booking\BookingFindDTO;

interface BookingManagerInterface extends CRUDManagerInterface
{
    /**
     * @param BookingFindDTO $data
     *
     * @return BookingFindDTO
     */
    public function buildMyBookingFindDTO(AbstractFindDTO $data): BookingFindDTO;
}
