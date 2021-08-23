<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class ConstraintAccount.
 *
 * @Annotation
 */
class ConstraintAccount extends Constraint
{
    public string $message = 'These dates are not allowed for booking';

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
