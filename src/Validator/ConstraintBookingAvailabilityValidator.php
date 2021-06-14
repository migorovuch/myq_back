<?php

namespace App\Validator;

use App\Entity\Booking;
use App\Model\DTO\Booking\BookingDTO;
use App\Model\DTO\Booking\BookingFindDTO;
use App\Model\Manager\BookingManagerInterface;
use App\Model\Manager\SpecialHoursManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ConstraintBookingAvailabilityValidator extends ConstraintValidator
{
    protected SpecialHoursManagerInterface $specialHoursManager;
    protected BookingManagerInterface $bookingManager;

    /**
     * ConstraintBookingAvailabilityValidator constructor.
     */
    public function __construct(
        SpecialHoursManagerInterface $specialHoursManager,
        BookingManagerInterface $bookingManager
    ) {
        $this->specialHoursManager = $specialHoursManager;
        $this->bookingManager = $bookingManager;
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

        if (!$value instanceof BookingDTO) {
            throw new UnexpectedValueException($value, BookingDTO::class);
        }

        $result = false;
        if (
            $value->getSchedule()->getEnabled() &&
            !$value->getSchedule()->getAvailable() &&
            $this->specialHoursManager->checkScheduleAvailability(
                $value->getSchedule(),
                $value->getStart(),
                $value->getEnd())
        ) {
            $selectedTimeBookings = $this->bookingManager->findByDTO(
                new BookingFindDTO(null, Booking::STATUS_ACCEPTED, $value->getSchedule(), $value->getStart(), $value->getEnd())
            );
            if (empty($selectedTimeBookings)) {
                $result = true;
            }
        }

        if (!$result) {
            $this->context->buildViolation($constraint->getMessage())
                ->atPath('start')
//                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
