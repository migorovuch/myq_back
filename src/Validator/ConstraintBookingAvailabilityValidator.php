<?php

namespace App\Validator;

use App\Entity\Booking;
use App\Model\DTO\Booking\BookingAvailabilityDTOInterface;
use App\Model\DTO\Booking\BookingDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Model\DTO\Booking\ChangeBookingDTO;
use App\Model\Manager\SpecialHoursManagerInterface;
use App\Repository\BookingRepository;
use DateInterval;
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
     * @param BookingDTO|ChangeBookingDTO   $value
     * @param ConstraintBookingAvailability $constraint
     *
     * @throws UnexpectedValueException|UnexpectedTypeException
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
            throw new UnexpectedValueException($value, BookingAvailabilityDTOInterface::class);
        }

        $schedule = $value->getSchedule();
        $filterFrom = clone $value->getStart();
        $filterTo = clone $value->getEnd();
        $timeBetweenBookingsInterval = new DateInterval('PT'.$schedule->getTimeBetweenBookings().'M');
        $filterFrom->sub($timeBetweenBookingsInterval);
        $filterTo->add($timeBetweenBookingsInterval);
        $selectedTimeBookings = $this->bookingRepository->findByDTO(
            new BookingFindDTO(null, Booking::STATUS_ACCEPTED, null, $schedule, null, null, $filterFrom, $filterTo)
        );

        $message = $this->translator->trans('These dates are not allowed for booking');
        if (
            !$schedule->getAvailable() &&
            !$this->specialHoursManager->checkScheduleAvailability(
                $schedule,
                $value->getStart(),
                $value->getEnd()
            )
        ) {
            // TODO: show message corresponding to mistake (invalid date/time/duration)
            $this->context->buildViolation($message)
                ->atPath('start')
                ->addViolation();
        } elseif (
            isset($selectedTimeBookings[1]) ||
            (
                !empty($selectedTimeBookings) &&
                (
                    !$value->getId() ||
                    (
                        $value->getId() &&
                        $value->getId() !== reset($selectedTimeBookings)->getId()
                    )
                )
            )
        ) {
            // TODO: show message corresponding to mistake (invalid date/time/duration)
            $this->context->buildViolation($message)
                ->atPath('start')
                ->addViolation();
        }
    }
}
