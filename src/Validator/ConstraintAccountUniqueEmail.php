<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class ConstraintAccountEmail.
 *
 * @Annotation
 */
class ConstraintAccountUniqueEmail extends Constraint
{
    public string $message = 'This email is already in use';

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
