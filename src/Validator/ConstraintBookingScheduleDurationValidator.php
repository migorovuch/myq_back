<?php


namespace App\Validator;

use App\Model\DTO\Booking\BookingAvailabilityDTOInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConstraintBookingScheduleDurationValidator extends ConstraintValidator
{

    /**
     * ConstraintBookingScheduleDurationValidator constructor.
     */
    public function __construct(protected TranslatorInterface $translator)
    {}

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConstraintBookingScheduleDuration) {
            throw new UnexpectedTypeException($constraint, ConstraintBookingScheduleDuration::class);
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
        $bookingDatesDifferent = abs($value->getStart()->getTimestamp() - $value->getEnd()->getTimestamp()) / 60;
        if (
            ($schedule->getBookingDuration() && $schedule->getBookingDuration() !== $bookingDatesDifferent) ||
            !(
                $schedule->getMinBookingTime() &&
                $schedule->getMinBookingTime() <= $bookingDatesDifferent &&
                $schedule->getMaxBookingTime() &&
                $schedule->getMaxBookingTime() >= $bookingDatesDifferent
            )
        ) {
            $message = $this->translator->trans('Incorrect booking duration');
            if ($schedule->getBookingDuration()) {
                $this->context->buildViolation($message)
                    ->atPath('start')
                    ->addViolation();
            } else {
                $this->context->buildViolation($message)
                    ->atPath('duration')
                    ->addViolation();
            }
        }
    }
}
