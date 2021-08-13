<?php

namespace App\Validator;

use App\Model\DTO\User\ChangeUserDTO;
use App\Model\DTO\User\UserDTO;
use App\Model\Manager\UserManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConstraintAccountUniqueEmailValidator extends ConstraintValidator
{
    protected UserManagerInterface $userManager;
    protected TranslatorInterface $translator;

    /**
     * ConstraintAccountEmailValidator constructor.
     * @param UserManagerInterface $userManager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        UserManagerInterface $userManager,
        TranslatorInterface $translator
    ) {

        $this->userManager = $userManager;
        $this->translator = $translator;
    }

    /**
     * @param UserDTO|ChangeUserDTO $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConstraintAccountUniqueEmail) {
            throw new UnexpectedTypeException($constraint, ConstraintAccountUniqueEmail::class);
        }
        if ($value->getEmail()) {
            $user = $this->userManager->findOneBy(['email' => $value->getEmail()]);
            if ($user) {
                $this->context->buildViolation($this->translator->trans('The client with this email already exists'))
                    ->atPath('email')
                    ->addViolation();
            }
        }
    }
}
