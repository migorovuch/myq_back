<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Entity\Company;
use App\Entity\CompanyChat;

interface CompanyChatManagerInterface extends CRUDManagerInterface
{
    public function getConfirmBookingActionString(CompanyChat $companyChat): string;

    public function getDeclineBookingActionString(CompanyChat $companyChat): string;

    public function sendNewBookingNotification(Company $company, Booking $booking);
}
