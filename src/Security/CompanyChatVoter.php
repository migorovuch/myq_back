<?php

namespace App\Security;

use App\Entity\Company;
use App\Entity\User;
use App\Model\Model\EntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CompanyChatVoter extends AbstractVoter
{
    protected function canCreate(UserInterface | string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    protected function canEdit(UserInterface | string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    protected function canView(UserInterface | string $currentUser, EntityInterface $subject): bool
    {
        return true;
    }

    protected function canDelete(UserInterface | string $currentUser, EntityInterface $subject): bool
    {
        return true;
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
