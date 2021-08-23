<?php


namespace App\Validator;


use App\Model\DTO\Booking\BookingAvailabilityDTOInterface;
use DateInterval;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConstraintBookingScheduleAcceptTimeValidator extends ConstraintValidator
{

    /**
     * ConstraintBookingScheduleAcceptTimeValidator constructor.
     */
    public function __construct(protected TranslatorInterface $translator)
    {}

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConstraintBookingScheduleAcceptTime) {
            throw new UnexpectedTypeException($constraint, ConstraintBookingScheduleAcceptTime::class);
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
        $acceptBookingTime = new DateTime();
        $acceptBookingTime->add(new DateInterval("PT" . $schedule->getAcceptBookingTime() . 'M'));
        if (!$schedule->getEnabled()) {
            $message = $this->translator->trans('Schedule is not available for booking');
            $this->context->buildViolation($message)
                ->atPath('start')
                ->addViolation();
            $this->context->buildViolation($message)
                ->atPath('schedule')
                ->addViolation();
        }
        if ($value->getStart() < $acceptBookingTime) {
            $diffInterval = $value->getStart()->diff($acceptBookingTime);
            $timeStr = '';
            if ($diffInterval->d > 0) {
                $timeStr = $this->translator->trans('%days% day(s) %hours% hour(s) %minutes% minutes', ['%days%' => $diffInterval->d, '%hours%' => $diffInterval->h, '%minutes%' => $diffInterval->i]);
            } elseif ($diffInterval->h > 0) {
                $timeStr = $this->translator->trans('%hours% hour(s) %minutes% minutes', ['%hours%' => $diffInterval->h, '%minutes%' => $diffInterval->i]);
            } elseif ($diffInterval->i > 0) {
                $timeStr = $this->translator->trans('%minutes% minutes', ['%minutes%' => $diffInterval->i]);
            }
            $message = $this->translator->trans('available for booking in').' '.$timeStr;

            $this->context->buildViolation($message)
                ->atPath('start')
                ->addViolation();
        }
    }
}
