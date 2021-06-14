<?php

namespace App\Security;

use App\Entity\Company;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CompanyVoter extends AbstractVoter
{

    protected function canCreate(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    protected function canEdit(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $subject->getUser()->getId() === $currentUser->getId();
    }

    protected function canView(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $this->canEdit($currentUser, $subject) || $subject->getStatus() === Company::STATUS_ON;
    }

    protected function canDelete(UserInterface|string $currentUser, EntityInterface $subject): bool
    {
        return $this->canEdit($currentUser, $subject);
    }

    protected function supports(string $attribute, $subject)
    {
        if (!\in_array($attribute, [static::VIEW, static::CREATE, static::UPDATE, static::DELETE])) {
            return false;
        }
        if (!$subject instanceof Company) {
            return false;
        }

        return true;
    }
}
