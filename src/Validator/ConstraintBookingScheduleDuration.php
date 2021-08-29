<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConstraintBookingScheduleDuration extends Constraint
{
    public string $message = 'Incorrect booking duration';

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
