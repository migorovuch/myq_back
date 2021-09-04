<?php

namespace App\Model\Manager;

use App\Entity\Booking;
use App\Model\DTO\Availability\AvailabilityFindDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Repository\BookingRepository;
use DateInterval;

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
     * {@inheritDoc}
     */
    public function findByDTO(AvailabilityFindDTO $data)
    {
        $bookings = $this->bookingRepository->findByDTO(new BookingFindDTO(
            null,
            Booking::STATUS_ACCEPTED,
            null,
            $data->getSchedule(),
            null,
            $data->getCompany(),
            $data->getFilterFrom(),
            $data->getFilterTo()
        ));
        $days = $this->specialHoursManager->getPeriodAvailability($data);
        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            $bookingStart = clone $booking->getStart();
            $bookingEnd = clone $booking->getEnd();
            $interval = new DateInterval('PT'.$data->getSchedule()->getTimeBetweenBookings().'M');
            $bookingStart->sub($interval);
            $bookingEnd->add($interval);
            $dayKey = $bookingStart->format('Y-m-d');
            if (!empty($days[$dayKey])) {
                $bookingFromTime = date('H:i', strtotime($bookingStart->format('H:i')));
                $bookingToTime = date('H:i', strtotime($bookingEnd->format('H:i')));
                $removeRanges = [];
                foreach ($days[$dayKey] as $rangeKay => $range) {
                    $rangeFrom = date('H:i', strtotime($range['from']));
                    $rangeTo = date('H:i', strtotime($range['to']));
                    if ($bookingFromTime <= $rangeFrom && $bookingToTime >= $rangeFrom) {
                        $days[$dayKey][$rangeKay]['from'] = $bookingEnd->format('H:i');
                    } elseif ($bookingFromTime <= $rangeTo && $bookingToTime >= $rangeTo) {
                        $days[$dayKey][$rangeKay]['to'] = $bookingStart->format('H:i');
                    } elseif ($bookingFromTime > $rangeFrom && $bookingToTime < $rangeTo) {
                        $days[$dayKey][] = [
                            'from' => $bookingEnd->format('H:i'),
                            'to' => $range['to'],
                        ];
                        $days[$dayKey][$rangeKay]['to'] = $bookingStart->format('H:i');
                    }
                    if (
                        date('H:i', strtotime($days[$dayKey][$rangeKay]['from'])) >=
                        date('H:i', strtotime($days[$dayKey][$rangeKay]['to']))
                    ) {
                        $removeRanges[] = $rangeKay;
                    }
                }
                foreach ($removeRanges as $rangeKay) {
                    array_splice($days[$dayKey], $rangeKay, 1);
                }
            }
        }

        return $days;
    }
}
