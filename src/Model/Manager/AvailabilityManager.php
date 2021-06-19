<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Model\DTO\Availability\AvailabilityFindDTO;
use App\Repository\BookingRepository;

class AvailabilityManager implements AvailabilityManagerInterface
{
    /**
     * AvailabilityManager constructor.
     */
    public function __construct(
        protected SpecialHoursManagerInterface $specialHoursManager,
        protected BookingRepository $bookingRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function findByDTO(AvailabilityFindDTO $data)
    {
        $bookings = $this->bookingRepository->findByDTO($data);
        $days = $this->specialHoursManager->getPeriodAvailability($data);
        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            $dayKey = $booking->getStart()->format('Y-m-d');
            if (!empty($days[$dayKey])) {
                $bookingFrom = date('H:i', strtotime($booking->getStart()->format('H:i')));
                $bookingTo = date('H:i', strtotime($booking->getEnd()->format('H:i')));
                foreach ($days[$dayKey] as $rangeKay=>$range) {
                    $rangeFrom = date('H:i', strtotime($range['from']));
                    $rangeTo = date('H:i', strtotime($range['to']));
                    if ($bookingFrom <= $rangeFrom && $bookingTo >= $rangeFrom) {
                        $days[$dayKey][$rangeKay]['from'] = $booking->getEnd()->format('H:i');
                    }elseif ($bookingFrom <= $rangeTo && $bookingTo >= $rangeTo) {
                        $days[$dayKey][$rangeKay]['to'] = $booking->getStart()->format('H:i');
                    }elseif ($bookingFrom > $rangeFrom && $bookingTo < $rangeTo) {
                        $days[$dayKey][] = [
                            'from' => $booking->getEnd()->format('H:i'),
                            'to' => $range['to']
                        ];
                        $days[$dayKey][$rangeKay]['to'] = $booking->getStart()->format('H:i');
                    }
                    if (date('H:i', strtotime($days[$dayKey][$rangeKay]['from'])) >= date('H:i', strtotime($days[$dayKey][$rangeKay]['to']))) {
                        unset($days[$dayKey][$rangeKay]);
                    }
                }
            }
        }

        return $days;
    }
}
