<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Entity\Company;

interface CompanyChatManagerInterface extends CRUDManagerInterface
{
    public function sendNewBookingNotification(Company $company, Booking $booking);
}
