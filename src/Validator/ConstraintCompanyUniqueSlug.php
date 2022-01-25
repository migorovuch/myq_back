<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class ConstraintCompanyUniqueSlug.
 *
 * @Annotation
 */
class ConstraintCompanyUniqueSlug extends Constraint
{
    public string $message = 'This slug is already in use';

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
