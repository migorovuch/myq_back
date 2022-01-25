<?php

namespace App\Validator;

use App\Model\DTO\Company\CompanyDTO;
use Symfony\Component\Validator\Constraint;
use App\Model\Manager\CompanyManagerInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConstraintCompanyUniqueSlugValidator extends ConstraintValidator
{
    protected CompanyManagerInterface $companyManager;
    protected TranslatorInterface $translator;

    /**
     * ConstraintCompanyUniqueSlugValidator constructor.
     *
     * @param CompanyManagerInterface $companyManager
     * @param TranslatorInterface  $translator
     */
    public function __construct(
        CompanyManagerInterface $companyManager,
        TranslatorInterface $translator
    ) {
        $this->companyManager = $companyManager;
        $this->translator = $translator;
    }

    /**
     * @param CompanyDTO $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConstraintCompanyUniqueSlug) {
            throw new UnexpectedTypeException($constraint, ConstraintCompanyUniqueSlug::class);
        }
        if (!$value->getSlug()) {
            return;
        }
        if ($this->companyManager->isSlugExists($value->getSlug(), $value->getId())) {
            $this->context->buildViolation($this->translator->trans('The company with this slug already exists'))
                ->atPath('slug')
                ->addViolation();
        }
    }
}
