<?php

namespace App\Model\Manager;

use App\Entity\Booking;
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

    /**
     * @param string $companyId
     * @param string $bookingId
     * @param int $status
     * @return mixed
     */
    public function changeBookingStatus(string $companyId, string $bookingId, int $status): Booking;
}
