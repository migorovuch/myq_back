<?php


namespace App\Validator;

use App\Model\DTO\User\ChangeAccountDTO;
use App\Model\Manager\UserManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class ConstraintAccountValidator extends ConstraintValidator
{
    protected UserPasswordEncoderInterface $userPasswordEncoder;
    protected Security $security;
    protected UserManagerInterface $userManager;
    private TranslatorInterface $translator;
    private EncoderFactoryInterface $encoderFactory;

    /**
     * ConstraintAccountValidator constructor.
     * @param UserManagerInterface $userManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param Security $security
     * @param TranslatorInterface $translator
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        UserManagerInterface $userManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        Security $security,
        TranslatorInterface $translator,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->security = $security;
        $this->userManager = $userManager;
        $this->translator = $translator;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param ChangeAccountDTO $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConstraintAccount) {
            throw new UnexpectedTypeException($constraint, ConstraintAccount::class);
        }
        if ($value->getPassword() && $value->getNewPassword()) {
            $user = $this->userManager->find($this->security->getUser()->getId());
            if (
                !$this->encoderFactory->getEncoder($user)->isPasswordValid(
                    $user->getPassword(),
                    $value->getPassword(),
                    $user->getSalt()
                )
            ) {
                $this->context->buildViolation($this->translator->trans('Incorrect credentials'))
                    ->atPath('password')
                    ->addViolation();
            }
        }
    }
}
