<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConstraintBookingScheduleAcceptTime extends Constraint
{
    public string $message = 'Next time to book';

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTargets()
    {
        return [
            self::CLASS_CONSTRAINT,
        ];
    }
}
