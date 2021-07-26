<?php

namespace App\Validator;

use App\Entity\Booking;
use App\Model\DTO\Booking\BookingAvailabilityDTOInterface;
use App\Model\DTO\Booking\BookingDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Model\Manager\SpecialHoursManagerInterface;
use App\Repository\BookingRepository;
use DateInterval;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConstraintBookingAvailabilityValidator extends ConstraintValidator
{
    protected SpecialHoursManagerInterface $specialHoursManager;
    protected BookingRepository $bookingRepository;
    protected TranslatorInterface $translator;

    /**
     * ConstraintBookingAvailabilityValidator constructor.
     */
    public function __construct(
        SpecialHoursManagerInterface $specialHoursManager,
        BookingRepository $bookingRepository,
        TranslatorInterface $translator
    ) {
        $this->specialHoursManager = $specialHoursManager;
        $this->bookingRepository = $bookingRepository;
        $this->translator = $translator;
    }

    /**
     * @param BookingDTO $value
     * @param ConstraintBookingAvailability $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConstraintBookingAvailability) {
            throw new UnexpectedTypeException($constraint, ConstraintBookingAvailability::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (!$value) {
            return;
        }

        if (!$value instanceof BookingAvailabilityDTOInterface) {
            throw new UnexpectedValueException($value, BookingDTO::class);
        }

        $now = new DateTime();
        $now->add(new DateInterval("PT" . $value->getSchedule()->getAcceptBookingTime() . 'M'));
        $result = false;
        $schedule = $value->getSchedule();
        $bookingDatesDifferent = abs($value->getStart()->getTimestamp() - $value->getEnd()->getTimestamp()) / 60;
        if (
            (
                $schedule->getBookingDuration() === $bookingDatesDifferent ||
                (
                    $schedule->getMinBookingTime() &&
                    $schedule->getMinBookingTime() <= $bookingDatesDifferent &&
                    $schedule->getMaxBookingTime() &&
                    $schedule->getMaxBookingTime() >= $bookingDatesDifferent
                )
            ) &&
            $schedule->getEnabled() &&
            $value->getStart() >= $now &&
            !$schedule->getAvailable() &&
            $this->specialHoursManager->checkScheduleAvailability(
                $schedule,
                $value->getStart(),
                $value->getEnd()
            )
        ) {
            $filterFrom = clone $value->getStart();
            $filterTo = clone $value->getEnd();
            $timeBetweenBookingsInterval = new DateInterval("PT" . $schedule->getTimeBetweenBookings() . 'M');
            $filterFrom->sub($timeBetweenBookingsInterval);
            $filterTo->add($timeBetweenBookingsInterval);
            $selectedTimeBookings = $this->bookingRepository->findByDTO(
                new BookingFindDTO(null, Booking::STATUS_ACCEPTED, $schedule, null, $filterFrom, $filterTo)
            );
            if (empty($selectedTimeBookings) || (!isset($selectedTimeBookings[1]) && $value->getId() && $value->getId() === reset($selectedTimeBookings)->getId())) {
                $result = true;
            }
        }

        if (!$result) {
            // TODO: show message corresponding to mistake (invalid date/time/duration)
            $this->context->buildViolation($this->translator->trans('These dates are not allowed for booking'))
                ->atPath('start')
//                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
