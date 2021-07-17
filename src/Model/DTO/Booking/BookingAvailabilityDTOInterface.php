<?php

namespace App\Model\DTO\Booking;

use App\Entity\Schedule;
use DateTime;

interface BookingAvailabilityDTOInterface
{
    public function getId(): ?string;

    public function getSchedule(): ?Schedule;

    public function getStart(): ?DateTime;

    public function getEnd(): ?DateTime;
}
